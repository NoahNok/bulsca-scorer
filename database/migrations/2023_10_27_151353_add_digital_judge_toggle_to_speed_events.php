<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competition_speed_events', function (Blueprint $table) {
            $table->boolean('digitalJudgeConfirmed')->default(false);
            $table->boolean('digitalJudgeEnabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competition_speed_events', function (Blueprint $table) {
            $table->dropColumn('digitalJudgeConfirmed');
            $table->dropColumn('digitalJudgeEnabled');
        });
    }
};
