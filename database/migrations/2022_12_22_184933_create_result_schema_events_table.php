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
        Schema::create('result_schema_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schema')->references('id')->on('result_schemas')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->morphs('event');
            $table->decimal('weight');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result_schema_events');
    }
};
