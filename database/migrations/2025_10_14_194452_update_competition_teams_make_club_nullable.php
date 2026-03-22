<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_teams', function (Blueprint $table) {
            // Step 1: Drop the foreign key constraint
            $table->dropForeign(['club']);

            // Step 2: Make the column nullable
            $table->foreignId('club')->nullable()->change();

            // Step 3: Re-add the foreign key constraint (optional)
            $table->foreign('club')
                ->references('id')
                ->on('clubs')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('competition_teams', function (Blueprint $table) {
            $table->dropForeign(['club']);
            $table->foreignId('club')->nullable(false)->change();
            $table->foreign('club')
                ->references('id')
                ->on('clubs')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }
};
