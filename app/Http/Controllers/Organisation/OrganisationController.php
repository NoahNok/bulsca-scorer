<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\UpdateScoringSettings;
use App\Http\Requests\Result\UpdateResultSchemaScoringSettings;
use App\Models\DQCode;
use App\Models\Event\ScoringSchema;
use App\Models\EventCode;
use App\Models\Organisation\Organisation;
use App\Models\PenaltyCode;
use App\Models\ResultSchemaTemplate;
use App\Models\SpeedEvent;
use Illuminate\Http\Request;

/**
 * An organisation's Scoring and Infractions, still Blade. Its other pages are Svelte, in Manage\.
 */
class OrganisationController extends Controller
{
    public function scoringSettings(Organisation $organisation)
    {


        return view('organisation.scoring', ['org' => $organisation]);
    }

    public function createScoringSchema(Organisation $organisation)
    {
        return view('organisation.scoring.create', ['org' => $organisation]);
    }

    public function createScoringSchemaPost(Organisation $organisation, UpdateScoringSettings $request)
    {
        $schema = new ScoringSchema();

        $schema->editFromRequest($request);

        $schema->organisation = $organisation->id;
        $schema->save();

        session()->flash('success', 'Created scoring schema');

        return response()->json(['url' => route('orgs.scoring.edit', ['organisation' => $organisation->name, 'schema' => $schema->id])]);
    }

    public function editScoringSchema(Organisation $organisation, ScoringSchema $schema)
    {


        if ($schema->organisation != $organisation->id) {
            abort(404);
        }



        return view('organisation.scoring.edit', ['org' => $organisation, 'schema' => $schema]);
    }

    public function editScoringSchemaPost(Organisation $organisation, ScoringSchema $schema, UpdateScoringSettings $request)
    {
        if ($schema->organisation != $organisation->id) {
            abort(404);
        }

        $schema->editFromRequest($request);

        $schema->save();

        return response()->json([]);
    }

    public function deleteScoringSchema(Organisation $organisation, ScoringSchema $schema)
    {
        if ($schema->organisation != $organisation->id) {
            abort(404);
        }

        $schema->delete();

        session()->flash('success', 'Schema Deleted');

        return response()->json([]);
    }


    public function infractions(Organisation $organisation)
    {

        $eventNames = SpeedEvent::pluck('name')->toArray();
        $eventNames[] = 'SERC';

        return view('organisation.infractions', ['org' => $organisation, 'dqs' => $organisation->disqualificationCodes()->paginate(20, ['*'], 'dq_page'), 'penalties' => $organisation->penaltyCodes()->paginate(20), 'eventNames' => $eventNames]);
    }

    public function getInfraction(Organisation $organisation, string $type, int $id)
    {
        if ($type == 'dq') {
            $code = $organisation->disqualificationCodes()->where('id', $id)->first();
        } elseif ($type == 'pen') {
            $code = $organisation->penaltyCodes()->where('id', $id)->first();
        } else {
            return response()->json(['error' => 'Invalid infraction type']);
        }

        if (!$code) {
            return response()->json(['error' => 'Infraction code not found']);
        }

        return response()->json([
            'id' => $code->id,
            'code' => "{$code}",
            'description' => $code->description,

            'events' => $code->eventCodes->mapWithKeys(function ($ec) {
                return [$ec->event => [
                    'id' => $ec->id,
                    'type' => $ec->type,
                ]];
            })->toArray()
        ]);
    }

    public function updateInfraction(Organisation $organisation, string $type, int $id, Request $request)
    {
        if ($type == 'dq') {
            $code = $organisation->disqualificationCodes()->where('id', $id)->first();
        } elseif ($type == 'pen') {
            $code = $organisation->penaltyCodes()->where('id', $id)->first();
        } else {
            return response()->json(['error' => 'Invalid infraction type']);
        }

        if (!$code) {
            return response()->json(['error' => 'Infraction code not found']);
        }

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'events' => 'required|array',
            'events.*' => 'array',
            'events.*.id' => 'nullable|integer',
            'events.*.type' => 'nullable|string|in:GENERIC,LANE,TURN,CHANGEOVER,CROSSLINE,BACKLINE,OOF,STARTER,PICKUP,null',
        ]);

        $code->description = $data['description'];
        $code->save();

        // Update event codes
        foreach ($data['events'] as $eventName => $eventData) {



            // Update existing event code
            $eventCode = EventCode::find($eventData['id']);
            if ($eventCode) {

                if ($eventData['type'] == null || $eventData['type'] == 'null') {
                    $eventCode->delete();
                    continue;
                }

                $eventCode->type = $eventData['type'];
                $eventCode->save();
            } else {
                // Create new event code
                if ($eventData['type'] == null) {
                    continue;
                }

                $newEventCode = new EventCode();
                $newEventCode->event = $eventName;
                $newEventCode->type = $eventData['type'];
                $newEventCode->pendq_id = $code->id;
                $newEventCode->pendq_type = get_class($code);
                $newEventCode->save();
            }
        }

        return response()->json([]);
    }

    public function createInfraction(Organisation $organisation, Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|in:dq,pen',
            'code' => 'required',
            'description' => 'required|string|max:255',
            'events' => 'required|array',
            'events.*' => 'array',
            'events.*.type' => 'nullable|string|in:GENERIC,LANE,TURN,CHANGEOVER,CROSSLINE,BACKLINE,OOF,STARTER,PICKUP,null',
        ]);

        $data['code'] = intval($data['code']);

        if ($data['type'] == 'dq') {
            $existing = $organisation->disqualificationCodes()->where('code', $data['code'])->first();
            if ($existing) {
                return response()->json(['error' => 'DQ code already exists']);
            }

            $code = new DQCode();
        } elseif ($data['type'] == 'pen') {
            $existing = $organisation->penaltyCodes()->where('code', $data['code'])->first();
            if ($existing) {
                return response()->json(['error' => 'Penalty code already exists']);
            }

            $code = new PenaltyCode();
        } else {
            return response()->json(['error' => 'Invalid infraction type']);
        }

        $code->code = $data['code'];
        $code->description = $data['description'];
        $code->organisation = $organisation->id;
        $code->save();

        // Create event codes
        foreach ($data['events'] as $eventName => $eventData) {
            if ($eventData['type'] == null) {
                continue;
            }

            $newEventCode = new EventCode();
            $newEventCode->event = $eventName;
            $newEventCode->type = $eventData['type'];
            $newEventCode->pendq_id = $code->id;
            $newEventCode->pendq_type = get_class($code);
            $newEventCode->save();
        }

        session()->flash('success', 'Infraction code created.');

        return response()->json([]);
    }

    public function deleteInfraction(Organisation $organisation, string $type, int $id)
    {
        if ($type == 'dq') {
            $code = $organisation->disqualificationCodes()->where('id', $id)->first();
        } elseif ($type == 'pen') {
            $code = $organisation->penaltyCodes()->where('id', $id)->first();
        } else {
            return response()->json(['error' => 'Invalid infraction type']);
        }

        if (!$code) {
            return response()->json(['error' => 'Infraction code not found']);
        }

        // Delete associated event codes
        foreach ($code->eventCodes as $eventCode) {
            $eventCode->delete();
        }

        $code->delete();

        session()->flash('success', 'Infraction code deleted.');

        return response()->json([]);
    }


    public function createResultSchemaTemplate(Organisation $organisation)
    {
        return view('organisation.scoring.result-schema.create', ['org' => $organisation]);
    }

    public function createResultSchemaTemplatePost(Organisation $organisation, UpdateResultSchemaScoringSettings $request)
    {
        $schema = new ResultSchemaTemplate();

        $schema->editFromRequest($request);

        $schema->organisation = $organisation->id;
        $schema->save();

        return response()->json(['url' => route('orgs.scoring.result-schema.edit', ['organisation' => $organisation->name, 'schema' => $schema->id])]);
    }

    public function editResultSchemaTemplate(Organisation $organisation, ResultSchemaTemplate $schema)
    {


        if ($schema->organisation != $organisation->id) {
            abort(404);
        }



        return view('organisation.scoring.result-schema.edit', ['org' => $organisation, 'schema' => $schema]);
    }

    public function editResultSchemaTemplatePost(Organisation $organisation, ResultSchemaTemplate $schema, UpdateResultSchemaScoringSettings $request)
    {
        if ($schema->organisation != $organisation->id) {
            abort(404);
        }

        $schema->editFromRequest($request);

        $schema->save();

        return response()->json([]);
    }
}
