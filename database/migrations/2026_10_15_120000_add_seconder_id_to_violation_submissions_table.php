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
        Schema::table('violation_submissions', function (Blueprint $table) {
            // seconder_name stays for submissions made before seconders were picked from the officials
            $table->foreignId('seconder_id')->nullable()->after('submitter_position')->constrained('users')->onUpdate("CASCADE")->onDelete("SET NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('violation_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seconder_id');
        });
    }
};
