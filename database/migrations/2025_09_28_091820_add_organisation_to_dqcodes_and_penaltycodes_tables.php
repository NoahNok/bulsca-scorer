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
        Schema::table('dq_codes', function (Blueprint $table) {
            $table->foreignId('organisation')->nullable()->references('id')->on('organisations')->onUpdate('CASCADE')->onDelete('cascade');
            $table->integer('code');
        });


        Schema::table('penalty_codes', function (Blueprint $table) {
            $table->foreignId('organisation')->nullable()->references('id')->on('organisations')->onUpdate('CASCADE')->onDelete('cascade');
            $table->integer('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dq_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organisation');
        });

        Schema::table('penalty_codes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organisation');
        });
    }
};
