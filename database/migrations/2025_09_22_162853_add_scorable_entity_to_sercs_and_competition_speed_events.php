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
        Schema::table('sercs', function (Blueprint $table) {
            $table->string('scorable_entity')->default('team');
        });

        Schema::table('competition_speed_events', function (Blueprint $table) {
            $table->string('scorable_entity')->default('team');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sercs', function (Blueprint $table) {
            $table->dropColumn('scorable_entity');
        });

        Schema::table('competition_speed_events', function (Blueprint $table) {
            $table->dropColumn('scorable_entity');
        });
    }
};
