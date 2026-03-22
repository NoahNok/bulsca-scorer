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
        Schema::table('result_schemas', function (Blueprint $table) {
            $table->json('schema');
            $table->dropColumn('league');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('result_schemas', function (Blueprint $table) {
            $table->dropColumn('schema');
            $table->text('league');
        });
    }
};
