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
        Schema::table('heats', function (Blueprint $table) {
            $table->foreignId('event')->nullable()->references('id')->on('competition_speed_events')->onUpdate('cascade')->onDelete('cascade');

            $table->dropForeign(['team']);

            $table->dropUnique(['team', 'heat', 'lane']);
            $table->unique(['team', 'heat', 'lane', 'event']);

            $table->foreign('team')->references('id')->on('competition_teams')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('heats', function (Blueprint $table) {

            $table->dropForeign(['team']);


            $table->dropUnique(['team', 'heat', 'lane', 'event']);
            $table->unique(['team', 'heat', 'lane']);

            $table->foreign('team')->references('id')->on('competition_teams')->onUpdate('cascade')->onDelete('cascade');

            $table->dropForeign(['event']);
            $table->dropColumn('event');
        });
    }
};
