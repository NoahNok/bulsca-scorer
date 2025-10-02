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
        Schema::table('competitors', function (Blueprint $table) {

            $table->dropConstrainedForeignId('club');
            $table->foreignId('team')->references('id')->on('competition_teams')->onUpdate('CASCADE')->onDelete('CASCADE');

            $table->dropColumn('league');
            $table->foreignId('league')->nullable()->references('id')->on('leagues')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team');
            $table->dropConstrainedForeignId('league');
            $table->text('league');
            $table->foreignId('club')->references('id')->on('clubs')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }
};
