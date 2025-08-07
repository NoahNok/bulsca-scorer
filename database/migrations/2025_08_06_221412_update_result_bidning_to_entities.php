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
        Schema::table('speed_results', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_team');
            $table->morphs('entity');
        });

        Schema::table('serc_results', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team');
            $table->morphs('entity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
