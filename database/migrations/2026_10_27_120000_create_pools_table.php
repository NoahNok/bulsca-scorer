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
        Schema::create('pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition')->references('id')->on('competitions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->string('name');
            $table->integer('lanes');
            $table->timestamps();
        });

        // Which leagues swim in each pool, in the order their heats run
        Schema::create('pool_league', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool')->references('id')->on('pools')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('league')->references('id')->on('leagues')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->boolean('is_pair')->default(false);
            $table->integer('order');
            $table->unique(['pool', 'league']);
            $table->timestamps();
        });

        // Heat numbers restart per pool, so a pool can't be removed while heats still use it
        Schema::table('heats', function (Blueprint $table) {
            $table->foreignId('pool')->nullable()->references('id')->on('pools')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->boolean('use_pools')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('use_pools');
        });

        Schema::table('heats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pool');
        });

        Schema::dropIfExists('pool_league');
        Schema::dropIfExists('pools');
    }
};
