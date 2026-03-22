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
        Schema::create('account_invites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('email');
            $table->morphs('to'); // e.g. org, comp etc
            $table->json('details')->nullable(); // e.g. {access: ['admin']}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_invites');
    }
};
