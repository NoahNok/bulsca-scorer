<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serc_tanks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serc')->constrained('sercs')->cascadeOnDelete();
            $table->integer('tank');
            $table->timestamps();
            $table->unique(['serc', 'tank']);
        });

        DB::table('draws')->select('serc', 'tank')->distinct()->orderBy('serc')->orderBy('tank')->get()
            ->each(function ($drawTank) {
                DB::table('serc_tanks')->insert([
                    'serc' => $drawTank->serc,
                    'tank' => $drawTank->tank,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('serc_tanks');
    }
};
