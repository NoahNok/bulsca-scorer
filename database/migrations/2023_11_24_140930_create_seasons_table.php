<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->timestamps();
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->foreignId('season')->nullable()->constrained('seasons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seasons');

        Schema::table('competitions', function (Blueprint $table) {
            $table->dropForeign(['season']);
            $table->dropColumn('season');
        });
    }
};
