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
        Schema::create('result_schema_templates', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->json('schema');
            $table->foreignId('organisation')->nullable()->references('id')->on('organisations')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_schema_templates');
    }
};
