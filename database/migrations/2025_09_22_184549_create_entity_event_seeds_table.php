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
        Schema::create('entity_event_seeds', function (Blueprint $table) {
            $table->id();

            $table->integer('seed');
            $table->morphs('entity');

            $table->foreignId('event')->references('id')->on('competition_speed_events')->onUpdate('CASCADE')->onDelete('CASCADE');


            $table->unique(['entity_id', 'entity_type', 'event']);

            $table->timestamps();
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->boolean('seed_per_event');
            $table->boolean('heats_per_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_event_seeds');

        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('seed_per_event');
            $table->dropColumn('heats_per_event');
        });
    }
};
