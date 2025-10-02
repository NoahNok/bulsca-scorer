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
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreignId('competition')->nullable(true)->references('id')->on('competitions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('league')->nullable()->references('id')->on('leagues')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition');
            $table->dropConstrainedForeignId('league');
        });
    }
};
