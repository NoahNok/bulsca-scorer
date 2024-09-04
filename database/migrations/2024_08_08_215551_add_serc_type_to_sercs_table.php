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
        Schema::table('sercs', function (Blueprint $table) {
            $table->enum('type', ['DRY', 'WET'])->default('DRY');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sercs', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
