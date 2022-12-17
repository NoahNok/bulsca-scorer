<?php

namespace Database\Seeders;

use App\Models\SpeedEvent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpeedEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = ['Swim & Tow', 'Rope Throw', 'Medley', 'Obstacle', 'Pool Lifesaver', 'Manikin'];

        foreach ($events as $event) {
            $se = SpeedEvent::firstOrCreate(['name' => $event]);
            $se->name = $event;
            $se->save();
        }
    }
}
