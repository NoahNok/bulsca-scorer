<?php

namespace Tests\Unit;

use App\Models\DigitalJudge\Violation\ViolationSubmission;
use Tests\TestCase;

class ViolationSubmissionModelTest extends TestCase
{
    public function test_violation_submission_has_expected_relationship_stubs(): void
    {
        $submission = new ViolationSubmission();

        $this->assertTrue(method_exists($submission, 'entity'));
        $this->assertTrue(method_exists($submission, 'event'));
        $this->assertTrue(method_exists($submission, 'submitted'));
        $this->assertTrue(method_exists($submission, 'applied'));
        $this->assertTrue(method_exists($submission, 'submitter'));
    }
}
