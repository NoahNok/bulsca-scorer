<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Without these, a competition with officials, or a user who has officiated or
     * submitted a DQ, can't be deleted.
     */
    public function up(): void
    {
        Schema::table('competition_officials', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['competition_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('competition_id')->references('id')->on('competitions')->cascadeOnDelete();
        });

        Schema::table('judge_dq_submissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_officials', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['competition_id']);

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('competition_id')->references('id')->on('competitions');
        });

        Schema::table('judge_dq_submissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
