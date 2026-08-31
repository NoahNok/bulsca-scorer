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
        Schema::create('serc_judge_league_restriction', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('judge_id')->constrained('serc_judges')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('sercs', function (Blueprint $table) {
            $table->boolean('use_restricted_judges')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serc_judge_league_restriction');
        Schema::table('sercs', function (Blueprint $table) {
            $table->dropColumn('use_restricted_judges');
        });
    }
};
