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
        Schema::table('judging_log', function (Blueprint $table) {
            $table->foreignId('judge')->nullable()->change();
            $table->foreignId('speed_event')->nullable()->references('id')->on('competition_speed_events')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('judging_log', function (Blueprint $table) {
            $table->dropConstrainedForeignId('speed_event');
        });
    }
};
