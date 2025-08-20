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
        Schema::table('competition_speed_events', function (Blueprint $table) {
            $table->foreignId('scoring_schema')->nullable()->references('id')->on('scoring_schemas')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('sercs', function (Blueprint $table) {
            $table->foreignId('scoring_schema')->nullable()->references('id')->on('scoring_schemas')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_speed_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('scoring_schema');
        });
        Schema::table('sercs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('scoring_schema');
        });
    }
};
