<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionSpeedEvent;
use App\Models\SpeedResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Saves a speed event's results as an Organiser types them into its grid: a time (or DNS, DNF, OOT, or
 * for Rope Throw how many ropes were in), a DQ code and a list of penalties per row. Every row is
 * checked before any is saved, so a mistake in one saves none, with an error on each cell that failed.
 */
class SpeedResultEntry
{
    private const DNS = 99904;
    private const DNF = 99915;
    private const OOT = 99901;

    public function __construct(private Competition $comp, private CompetitionSpeedEvent $event) {}

    /**
     * @param array<int|string, array{result?: ?string, disqualification?: ?string, penalties?: ?string}> $rows by result id
     */
    public function save(array $rows): void
    {
        $results = $this->event->results()->with('entity')->whereIn('id', array_keys($rows))->get()->keyBy('id');
        abort_if($results->count() != count($rows), 404);

        $parsed = [];
        $errors = [];

        foreach ($rows as $id => $row) {
            $cellErrors = [];
            $parsed[$id] = $this->parse($row, $cellErrors);

            foreach ($cellErrors as $cell => $message) {
                $errors["rows.$id.$cell"] = $message;
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($parsed, $results) {
            foreach ($parsed as $id => $row) {
                $this->apply($results[$id], $row);
            }
        });
    }

    /**
     * @param array<string, string> $errors what to enter instead, by the cell that's wrong
     * @return array{result: int|null, clear: bool, dq: int|null, penalties: int[]|null}
     */
    private function parse(array $row, array &$errors): array
    {
        $dq = strtoupper(trim($row['disqualification'] ?? ''));
        $result = strtoupper(trim($row['result'] ?? ''));

        $dqCode = null;
        if (preg_match('/^DQ([0-9]{1,4})$/', $dq, $match)) {
            $dqCode = (int) $match[1];
        } elseif ($dq !== '') {
            $errors['disqualification'] = 'Enter a DQ code, like DQ101.';
        }

        // Penalties are only sent for events that have them; null leaves them alone
        $penalties = null;
        if (array_key_exists('penalties', $row)) {
            $penalties = [];
            foreach (array_filter(array_map('trim', explode(',', $row['penalties'] ?? ''))) as $penalty) {
                if (!preg_match('/^P([0-9]{1,3})$/i', $penalty, $match)) {
                    $errors['penalties'] = 'Enter penalty codes split by commas, like P100, P200.';
                    break;
                }
                $penalties[] = (int) $match[1];
            }
        }

        // DNS, DNF and OOT go in the time cell, and are saved as DQs over the one entered
        $special = match (true) {
            str_starts_with($result, 'DNS') => self::DNS,
            str_starts_with($result, 'DN') => self::DNF,
            str_starts_with($result, 'OOT') => self::OOT,
            default => null,
        };

        return [
            'result' => $special || $result === '' ? null : $this->parseTime($result, $errors),
            'clear' => $result === '',
            'dq' => $special ?? $dqCode,
            'penalties' => $penalties,
        ];
    }

    /**
     * A time as mm:ss.hh or mm:ss.hhh in milliseconds, or for Rope Throw also 0-3 ropes in
     */
    private function parseTime(string $value, array &$errors): ?int
    {
        if ($this->event->getName() == 'Rope Throw' && preg_match('/^[0-3]$/', $value)) {
            return (int) $value;
        }

        if (!preg_match('/^([0-9]{1,2}):([0-9]{1,2})\.([0-9]{2,3})$/', $value, $match)) {
            $errors['result'] = $this->event->getName() == 'Rope Throw'
                ? 'Enter a time like 01:23.45, or 0-3 for how many ropes were in.'
                : 'Enter a time like 01:23.45, or DNS, DNF or OOT.';

            return null;
        }

        $millis = strlen($match[3]) == 2 ? (int) $match[3] * 10 : (int) $match[3];

        return (int) $match[1] * 60000 + (int) $match[2] * 1000 + $millis;
    }

    private function apply(SpeedResult $result, array $row): void
    {
        // Read before any DQ changes, as DNS, DNF and OOT are entered as DQs
        $before = $result->getHistoryTime();

        $this->event->clearEntityDisqualifications($result->entity);
        if ($row['dq'] !== null) {
            $this->event->addEntityDisqualification($result->entity, $row['dq']);
        }

        if ($row['penalties'] !== null) {
            $this->event->clearEntityPenalties($result->entity);
            foreach ($row['penalties'] as $code) {
                $this->event->addEntityPenalty($result->entity, $code);
            }
        }

        // DNS, DNF and OOT keep the time underneath; clearing the cell clears it
        if ($row['result'] !== null || $row['clear']) {
            $result->result = $row['result'];
            $result->save();
        }

        $after = $result->getHistoryTime();
        if ($before === $after) return;

        $result->recordActivity('SPEED_RESULT_UPDATED', "Time changed from {$before} to {$after}", context: [
            'changes' => ['result' => ['old' => $before, 'new' => $after]],
        ], related: [$this->event, $result->entity, $this->comp]);
    }
}
