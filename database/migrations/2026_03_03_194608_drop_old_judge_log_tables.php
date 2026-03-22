<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('judging_log');
        Schema::dropIfExists('better_judge_logs');
    }

    /**
     * Reverse the migrations.
     * 
     * THIS MIGRATION IS NOT TO BE ROLLED BACK. IT IS ONLY TO DROP THE OLD JUDGE LOG TABLES, WHICH ARE NO LONGER USED.
     */
    public function down(): void
    {
        //
    }
};
