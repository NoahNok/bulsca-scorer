<?php

namespace Database\Seeders;

use App\Models\DQCode;
use App\Models\EventCode;
use App\Models\PenaltyCode;
use App\Models\SpeedEvent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DQPenLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            'Swim & Tow' => [
                'dq' => [1, 2, 3, 4, 5, 6, 7, 8, 9 => 'OOF', 10 => 'STARTER', 14 => 'TURN', 14 => 'PICKUP', 15, 17, 40, 41, 50, 101, 102, 103, 104, 105, 106, 201, 202, 203, 205, 206, 501, 502 => 'TURN', 503 => 'PICKUP'],
                'pen' => [900, 901, 902 => 'LANE', 903 => 'PICKUP', 904 => 'PICKUP', 905 => 'PICKUP', 906, 907 => 'LANE', 908 => 'LANE', 909 => 'LANE', 910 => 'LANE', 911 => 'LANE', 912 => 'LANE', 913 => 'LANE', 914 => 'LANE', 915 => 'PICKUP']
            ],
            'Rope Throw' => [
                'dq' => [1, 2, 3, 4, 5, 6, 7, 8, 9 => 'OOF', 10 => 'STARTER', 15, 17, 40, 41, 50, 53 => 'OOF', 58 => 'STARTER', 101, 102, 103, 104, 105, 106, 201, 202, 203, 205, 206, 401 => 'CROSSLINE', 402, 403 => 'CROSSLINE', 403 => 'OOF'],
                'pen' => [801 => 'CROSSLINE', 802 => 'CROSSLINE', 803 => 'LANE', 804 => 'LANE', 805 => 'LANE', 806 => 'LANE', 807 => 'LANE', 808 => 'BACKLINE', 809 => 'CROSSLINE', 810, 811 => 'CROSSLINE'],
            ],
            'Medley' => [
                'dq' => [1, 2, 3, 4, 5, 6, 7, 8, 9 => 'OOF', 10 => 'STARTER', 14 => 'TURN', 14 => 'PICKUP', 15, 17, 40, 41 => 'CHANGEOVER', 45, 46 => 'LANE', 47 => 'LANE', 48 => 'LANE', 49 => 'LANE', 50, 101, 102, 103, 104, 105, 106, 201, 202, 203, 205, 206, 601 => 'TURN'],
                'pen' => [902 => 'LANE'],
            ],
            'Obstacle' => [
                'dq' => [1, 2, 3, 4, 5, 6, 7, 8, 9 => 'OOF', 10 => 'STARTER', 11 => 'LANE', 12 => 'LANE', 13 => 'LANE', 14 => 'TURN', 15, 17, 40, 41 => 'CHANGEOVER', 50, 101, 102, 103, 104, 105, 106, 201, 202, 203, 205, 208],
            ],
            'Pool Lifesaver' => [
                'dq' => [1, 2, 3, 4, 5, 6, 7, 8, 9 => 'OOF', 10 => 'STARTER', 15, 17, 19 => 'LANE', 20 => 'LANE', 39, 40, 41 => 'CHANGEOVER', 42 => 'CHANGEOVER', 43 => 'CHANGEOVER', 50, 59 => 'CHANGEOVER', 60 => 'LANE', 61 => 'LANE', 101, 102, 103, 104, 105, 106, 201, 202, 203, 205, 206, 602],
            ],
            'Manikin' => [
                'dq' => [1, 2, 3, 4, 5, 6, 7, 8, 9 => 'OOF', 10 => 'STARTER', 14, 15, 17, 19 => 'LANE', 20 => 'LANE', 21 => 'LANE', 39, 40, 41 => 'CHANGEOVER', 42 => 'CHANGEOVER', 43 => 'CHANGEOVER', 50, 101, 102, 103, 104, 105, 106, 201, 202, 203, 205, 206],
                'pen' => [902 => 'LANE'],
            ],
            'SERC' => [
                'dq' => [1, 2, 3, 4, 5, 6, 7, 101, 102, 103, 104, 105, 106, 301, 302, 303, 304, 305, 306],
                'pen' => [701, 702],
            ]
        ];

        foreach ($events as $eventName => $values) {


            foreach ($values['dq'] as $dq => $type) {


                if (!is_string($type)) {
                    $dq = $type;
                    $type = null;
                }


                $dqCode = DQCode::find($dq);

                if (!$dqCode) {
                    continue;
                }

                $ec = EventCode::firstOrCreate([
                    'event' => $eventName,
                    'pendq_id' => $dqCode->id,
                    'pendq_type' => DQCode::class,
                ]);

                if ($type) {
                    $ec->type = $type;
                }



                $ec->save();
            }

            if (array_key_exists('pen', $values)) {
                foreach ($values['pen'] as $pen => $type) {

                    if (!is_string($type)) {
                        $pen = $type;
                        $type = null;
                    }

                    $penCode = PenaltyCode::find($pen);

                    if (!$penCode) {
                        continue;
                    }

                    $ec = EventCode::firstOrCreate([
                        'event' => $eventName,
                        'pendq_id' => $penCode->id,
                        'pendq_type' => PenaltyCode::class,
                    ]);

                    if ($type) {
                        $ec->type = $type;
                    }

                    $ec->save();
                }
            }
        }
    }
}
