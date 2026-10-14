<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->timestamp('sercs_released_at')->nullable();
        });

        // Competitions that have already happened shouldn't suddenly hide their SERCs from officials
        DB::table('competitions')->whereDate('when', '<', now()->toDateString())->update(['sercs_released_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('sercs_released_at');
        });
    }
};
