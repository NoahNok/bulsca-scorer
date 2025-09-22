<?php

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

        Schema::table('event_oofs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('heat_lane');
        });

        Schema::table('judge_dq_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('heat_lane');
        });

        Schema::dropIfExists('heats');

        Schema::table('competition_teams', function (Blueprint $table) {
            $table->dropColumn('serc_order');
            $table->dropColumn('serc_tank');
        });

        Schema::create('heats', function (Blueprint $table) {
            $table->id();

            $table->morphs('event');
            $table->morphs('entity');

            $table->integer('heat');
            $table->integer('lane');

            $table->unique(['event_id', 'event_type', 'entity_id', 'entity_type']);

            $table->timestamps();
        });

        Schema::create('draws', function (Blueprint $table) {
            $table->id();

            $table->morphs('event');
            $table->morphs('entity');

            $table->integer('tank');
            $table->integer('draw');

            $table->unique(['event_id', 'event_type', 'entity_id', 'entity_type']);

            $table->timestamps();
        });

        Schema::table('event_oofs', function (Blueprint $table) {
            $table->foreignId('heat_lane')->references('id')->on('heats')->onUpdate('CASCADE')->onDelete('CASCADE');
        });

        Schema::table('judge_dq_submissions', function (Blueprint $table) {
            $table->foreignId('heat_lane')->nullable()->constrained('heats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heats');
        Schema::dropIfExists('draws');
    }
};
