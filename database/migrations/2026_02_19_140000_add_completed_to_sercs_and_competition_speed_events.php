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
        if (!Schema::hasColumn('sercs', 'completed')) {
            Schema::table('sercs', function (Blueprint $table) {
                $table->boolean('completed')->default(false)->after('digitalJudgeConfirmed');
            });
        }

        if (!Schema::hasColumn('competition_speed_events', 'completed')) {
            Schema::table('competition_speed_events', function (Blueprint $table) {
                $table->boolean('completed')->default(false)->after('digitalJudgeConfirmed');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('sercs', 'completed')) {
            Schema::table('sercs', function (Blueprint $table) {
                $table->dropColumn('completed');
            });
        }

        if (Schema::hasColumn('competition_speed_events', 'completed')) {
            Schema::table('competition_speed_events', function (Blueprint $table) {
                $table->dropColumn('completed');
            });
        }
    }
};
