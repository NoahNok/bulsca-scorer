<?php

namespace App\Http\Requests\DigitalJudge\Violation;

use App\Models\DigitalJudge\Violation\ViolationSubmission;

class ResubmitViolationRequest extends SubmitViolationRequest
{
    /**
     * Only the original submitter can resubmit, and only once it has been rejected
     */
    public function authorize(): bool
    {
        $competition = $this->route('competition');
        $submission = $this->route('submission');

        return $submission instanceof ViolationSubmission
            && $submission->competition_id == $competition->id
            && $submission->canBeResubmittedBy($this->user());
    }
}
