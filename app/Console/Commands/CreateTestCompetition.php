<?php

namespace App\Console\Commands;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Competition\CompetitionScoringSettings;
use App\Models\CompetitionSpeedEvent;
use App\Models\CompetitionTeam;
use App\Models\League;
use App\Models\Orders\EntityEventSeed;
use App\Models\SERC;
use App\Models\SERC\MarkingPointTemplate;
use App\Models\SERCJudge;
use App\Models\SERCMarkingPoint;
use App\Models\SpeedEvent;
use App\Models\SpeedResult;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTestCompetition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competition:create-test {--name=Test Competition} {--owner= : Email of the user to make owner}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a test competition with leagues, club teams, two speed events and a SERC';

    private const CLUBS = ['Warwick', 'Birmingham', 'Loughborough', 'Nottingham', 'Oxford'];

    private const OBJECTIVES = [
        'Casualty 1 - Unconscious' => ['Checks for danger', 'Checks for response', 'Opens airway', 'Places in recovery position'],
        'Casualty 2 - Bleeding' => ['Applies direct pressure', 'Elevates the wound', 'Reassures casualty'],
        'Casualty 3 - Weak swimmer' => ['Talks casualty to safety', 'Uses a throwing aid', 'Keeps eye contact'],
        'Overall Control' => ['Calls for help', 'Organises bystanders', 'Prioritises casualties'],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $owner = null;
        if ($this->option('owner')) {
            $owner = User::where('email', $this->option('owner'))->first();

            if ($owner === null) {
                $this->error("No user exists with email {$this->option('owner')}.");
                return Command::FAILURE;
            }
        }

        $comp = DB::transaction(function () use ($owner) {
            $comp = new Competition();
            $comp->name = $this->option('name');
            $comp->when = now()->addWeek();
            $comp->where = 'Test Pool';
            $comp->max_lanes = 8;
            $comp->anytimepin = false;
            $comp->seed_per_event = false;
            $comp->heats_per_event = false;
            $comp->use_seeds = true;
            $comp->save();

            if ($owner) {
                $comp->addAccount($owner, ['owner']);
            }

            $css = new CompetitionScoringSettings();
            $css->use_tanks = true;
            $css->competition = $comp->id;
            $css->save();

            $leagues = collect(['A', 'B'])->mapWithKeys(function ($name) use ($comp) {
                $league = new League();
                $league->name = $name;
                $league->competition = $comp->id;
                $league->save();

                return [$name => $league];
            });

            $this->createEntries($comp, $leagues);
            $this->createSpeedEvents($comp);
            $this->createSeeds($comp);
            $this->createSerc($comp);

            return $comp;
        });

        $this->info("Created '{$comp->name}': " . route('comps.view', $comp));

        return Command::SUCCESS;
    }

    private function createEntries(Competition $comp, $leagues): void
    {
        $clubWithoutA = collect(self::CLUBS)->random();

        foreach (self::CLUBS as $clubName) {
            $club = new Club();
            $club->name = $clubName;
            $club->competition = $comp->id;
            $club->save();

            // the club without an A team starts at B, so its first team is in league B
            $firstLetter = $clubName === $clubWithoutA ? 1 : 0;
            $teamCount = rand(1, 5);

            for ($i = 0; $i < $teamCount; $i++) {
                $letter = chr(ord('A') + $firstLetter + $i);

                $team = new CompetitionTeam();
                $team->competition = $comp->id;
                $team->club = $club->id;
                $team->team = $letter;
                $team->save();

                $team->leagues()->attach($leagues[$letter === 'A' ? 'A' : 'B']->id);

                $this->line("  {$clubName} {$letter} (league " . ($letter === 'A' ? 'A' : 'B') . ")");
            }
        }
    }

    private function createSpeedEvents(Competition $comp): void
    {
        foreach (['Swim & Tow', 'Rope Throw'] as $eventName) {
            $speedEvent = SpeedEvent::where('name', $eventName)->firstOrFail();

            $cse = new CompetitionSpeedEvent();
            $cse->event = $speedEvent->id;
            $cse->competition = $comp->id;
            $cse->scorable_entity = 'team';
            $cse->record = $speedEvent->record;
            $cse->weight = 1;
            $cse->save();

            foreach ($cse->getScorableEntities() as $team) {
                $sr = new SpeedResult();
                $sr->entity()->associate($team);
                $sr->event = $cse->id;
                $sr->save();
            }
        }
    }

    private function createSeeds(Competition $comp): void
    {
        // without seed per event, a team's one seed is stored against the first speed event
        $event = $comp->getSpeedEvents()->first();

        foreach ($event->getScorableEntities() as $team) {
            $seed = new EntityEventSeed();
            $seed->entity()->associate($team);
            $seed->speed_event = $event->id;
            $seed->seed = rand(6000, 17000) * 10; // 1:00.00 to 2:50.00
            $seed->save();
        }
    }

    private function createSerc(Competition $comp): void
    {
        $template = MarkingPointTemplate::where('name', '1-10')->first();

        $serc = new SERC();
        $serc->competition = $comp->id;
        $serc->name = 'Dry SERC';
        $serc->type = 'DRY';
        $serc->scorable_entity = 'team';
        $serc->save();

        foreach (collect(self::OBJECTIVES)->random(2, true) as $objectiveName => $criteria) {
            $judge = new SERCJudge();
            $judge->name = $objectiveName;
            $judge->description = '';
            $judge->serc = $serc->id;
            $judge->save();

            foreach (collect($criteria)->random(rand(1, 2)) as $criterion) {
                $mp = new SERCMarkingPoint();
                $mp->name = $criterion;
                $mp->weight = rand(1, 3);
                $mp->judge = $judge->id;
                $mp->serc = $serc->id;
                $mp->marking_point_template_id = $template?->id;
                $mp->save();
            }
        }
    }
}
