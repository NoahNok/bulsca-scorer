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
        Schema::table('leagues', function(Blueprint $table) {
            $table->dropColumn('scoring_type');
            $table->foreignId('competition')->references('id')->on('competitions')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leagues', function(Blueprint $table) {
            $table->dropConstrainedForeignId('competition');
        });
    }
};
