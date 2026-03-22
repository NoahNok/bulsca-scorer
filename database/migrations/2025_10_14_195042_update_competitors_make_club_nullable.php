<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitors', function (Blueprint $table) {
            // Step 1: Drop the foreign key constraint
            $table->dropForeign(['team']);

            // Step 2: Make the column nullable
            $table->foreignId('team')->nullable()->change();

            // Step 3: Re-add the foreign key constraint (optional)
            $table->foreign('team')
                ->references('id')
                ->on('competition_teams')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('competitors', function (Blueprint $table) {
            $table->dropForeign(['team']);
            $table->foreignId('team')->nullable(false)->change();
            $table->foreign('team')
                ->references('id')
                ->on('competition_teams')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }
};
