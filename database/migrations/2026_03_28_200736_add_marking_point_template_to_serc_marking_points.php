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
        Schema::table('serc_marking_points', function (Blueprint $table) {
            $table->foreignUuid('marking_point_template_id')->nullable()->constrained('marking_point_templates')->nullOnDelete()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serc_marking_points', function (Blueprint $table) {
            $table->dropConstrainedForeignId('marking_point_template_id');
        });
    }
};
