<?php

namespace App\Http\Controllers\DigitalJudge\Violation;

use App\Http\Controllers\Controller;
use App\Http\Requests\DigitalJudge\Violation\UpdateViolationStateRequest;
use App\Models\Competition;
use App\Models\DigitalJudge\Violation\ViolationSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ViolationStateController extends Controller
{
    public function updateState(Competition $competition, ViolationSubmission $submission, UpdateViolationStateRequest $updateViolationStateRequest)
    {
        $validated = $updateViolationStateRequest->validated();

        $submission->updateStatus($validated['state']);


        return response()->json(['state' => $submission->status]);
    }
}
