<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Allow UUID keyed models (e.g. violation submissions) to be related to activities.
     */
    public function up(): void
    {
        Schema::table('activity_relations', function (Blueprint $table) {
            $table->string('related_id', 36)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('related_id')->change();
        });
    }
};
