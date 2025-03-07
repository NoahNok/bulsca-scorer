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
        Schema::create('overall_judge_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('judge')->references('id')->on('serc_judges')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->text('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overall_judge_notes');
    }
};
