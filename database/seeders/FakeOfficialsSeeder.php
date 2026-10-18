<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Local test data for the Officials tab. Adds a mix of fake officials to the newest competition
 * (or COMPETITION_ID). Safe to run again: accounts are matched by email.
 *
 *   php artisan db:seed --class=FakeOfficialsSeeder
 */
class FakeOfficialsSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('Not seeding fake officials in production.');
            return;
        }

        $competition = env('COMPETITION_ID')
            ? Competition::findOrFail(env('COMPETITION_ID'))
            : Competition::orderByDesc('id')->firstOrFail();

        // name, email, role, judge only, has signed in, added this many days ago
        $officials = [
            ['Alex Morgan', 'alex.morgan@example.com', 'referee', true, true, 6],
            ['Priya Shah', 'priya.shah@example.com', 'official', true, true, 5],
            ['Tom Fletcher', 'tom.fletcher@example.com', 'official', true, true, 5],
            ['Hannah Okafor', 'hannah.okafor@example.com', 'official', false, true, 3],
            ['Jamie Lee', 'jamie.lee@example.com', 'official', true, false, 2],
            ['Sam Whitaker', 'sam.whitaker@example.com', 'official', true, false, 0],
        ];

        foreach ($officials as [$name, $email, $role, $judgeOnly, $signedIn, $daysAgo]) {
            $user = User::firstOrNew(['email' => $email]);
            $user->name = $name;
            $user->password ??= Hash::make('password');
            $user->judge_only = $judgeOnly;
            $user->email_verified_at = $signedIn ? now()->subDays($daysAgo) : null;
            $user->save();

            $added = now()->subDays($daysAgo);
            $competition->officials()->syncWithoutDetaching([
                $user->id => ['role' => $role, 'created_at' => $added, 'updated_at' => $added],
            ]);
        }

        $this->command->info(count($officials) . " fake officials added to {$competition->name} (password: password).");
    }
}
