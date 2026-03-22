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
            $table->dropConstrainedForeignId('brand');
        });
        Schema::dropIfExists('brand_users');
        Schema::dropIfExists('brands');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // not implemented
    }
};
