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
        Schema::table('judge_dq_submissions', function (Blueprint $table) {
            $table->boolean('appealed')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('judge_dq_submissions', function (Blueprint $table) {
            $table->dropColumn('appealed');
        });
    }
};
