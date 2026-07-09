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
        Schema::table('competitions', function (Blueprint $table) {
            $table->foreignId('championship_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', ['STANDALONE', 'REGIONAL', 'QUALIFIER', 'FINAL'])->default('STANDALONE')->after('championship_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('championship_id');
            $table->dropColumn('type');
        });
    }
};
