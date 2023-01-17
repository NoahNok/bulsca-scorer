<?php

namespace Database\Seeders;

use App\Models\League;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        League::firstOrCreate(['name' => 'S']);
        League::firstOrCreate(['name' => 'F']);
        League::firstOrCreate(['name' => 'NC']);
        League::firstOrCreate(['name' => 'NS']);
        League::firstOrCreate(['name' => 'OB']);
    }
}
