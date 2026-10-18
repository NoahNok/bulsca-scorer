<?php

use App\Models\DigitalJudge\Violation\ViolationSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('violation_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('competition_id');
            $table->morphs('entity');
            $table->morphs('event');
            $table->morphs('submitted');
            $table->nullableMorphs('applied');
            $table->enum('status', ViolationSubmission::$STATES)->default("SUBMITTED");
            $table->smallInteger('turn')->nullable();
            $table->smallInteger('length')->nullable();
            $table->text('details');
            $table->foreignId('submitter_id')->nullable()->constrained('users')->onUpdate("CASCADE")->onDelete("SET NULL");
            $table->text('submitter_position');
            $table->string('seconder_name')->nullable();
            $table->string('seconder_position')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violation_submissions');
    }
};
