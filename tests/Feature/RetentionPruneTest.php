<?php

namespace Tests\Feature;

use ALajusticia\Logins\Models\Login;
use App\Models\Competition;
use App\Models\CompetitionVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The daily retention prune keeps the promises in the privacy policy: login records go after 12
 * months without activity, and WhatIf competitions and accounts go after 14 days unused.
 */
class RetentionPruneTest extends TestCase
{
    use RefreshDatabase;

    private function login(User $user, $lastActive): Login
    {
        $login = new Login();
        $login->authenticatable()->associate($user);
        $login->last_activity_at = $lastActive;
        $login->expires_at = now()->addYear();
        $login->save();

        return $login;
    }

    private function loginExists(Login $login): bool
    {
        return Login::withoutGlobalScopes()->withTrashed()->whereKey($login->id)->exists();
    }

    public function test_logins_inactive_for_over_a_year_are_deleted(): void
    {
        $user = User::factory()->create();
        $stale = $this->login($user, now()->subMonths(13));
        $recent = $this->login($user, now()->subMonths(11));

        $this->artisan('retention:prune')->assertSuccessful();

        $this->assertFalse($this->loginExists($stale));
        $this->assertTrue($this->loginExists($recent));
    }

    public function test_logins_signed_out_over_a_month_ago_are_deleted(): void
    {
        $user = User::factory()->create();
        $old = $this->login($user, now()->subMonths(2));
        $old->delete();
        Login::withoutGlobalScopes()->withTrashed()->whereKey($old->id)->update(['deleted_at' => now()->subMonths(2)]);
        $recent = $this->login($user, now());
        $recent->delete();

        $this->artisan('retention:prune')->assertSuccessful();

        $this->assertFalse($this->loginExists($old));
        $this->assertTrue($this->loginExists($recent));
    }

    public function test_competition_visits_over_a_year_old_are_deleted(): void
    {
        $user = User::factory()->create();
        $comp = new Competition();
        $comp->name = 'Visited';
        $comp->when = now();
        $comp->where = 'Test Pool';
        $comp->max_lanes = 8;
        $comp->anytimepin = false;
        $comp->seed_per_event = false;
        $comp->heats_per_event = false;
        $comp->use_seeds = true;
        $comp->save();
        $other = $comp->replicate();
        $other->save();

        $stale = CompetitionVisit::create(['user_id' => $user->id, 'competition_id' => $comp->id, 'visited_at' => now()->subMonths(13)]);
        $recent = CompetitionVisit::create(['user_id' => $user->id, 'competition_id' => $other->id, 'visited_at' => now()->subMonths(11)]);

        $this->artisan('retention:prune')->assertSuccessful();

        $this->assertModelMissing($stale);
        $this->assertModelExists($recent);
    }

    public function test_unused_whatif_competitions_and_accounts_are_deleted(): void
    {
        // package migrations pick their own connection, so point everything at WhatIf to migrate it
        config(['database.default' => 'whatif', 'webpush.database_connection' => 'whatif']);
        $this->artisan('migrate:fresh');
        config(['database.default' => 'mysql', 'webpush.database_connection' => 'mysql']);

        $whatif = DB::connection('whatif');
        $stale = $whatif->table('users')->insertGetId(['name' => 'Stale', 'email' => 'stale@example.com', 'password' => 'x', 'created_at' => now()->subMonth(), 'updated_at' => now()->subMonth()]);
        $active = $whatif->table('users')->insertGetId(['name' => 'Active', 'email' => 'active@example.com', 'password' => 'x', 'created_at' => now()->subMonth(), 'updated_at' => now()->subMonth()]);
        $new = $whatif->table('users')->insertGetId(['name' => 'New', 'email' => 'new@example.com', 'password' => 'x', 'created_at' => now(), 'updated_at' => now()]);

        foreach ([[$stale, now()->subDays(15)], [$active, now()->subDay()]] as [$user, $updated]) {
            $whatif->table('competitions')->insert([
                'name' => 'Clone', 'when' => now(), 'where' => 'Pool', 'max_lanes' => 8, 'anytimepin' => false,
                'seed_per_event' => false, 'heats_per_event' => true, 'use_seeds' => true,
                'wi_user' => $user, 'created_at' => $updated, 'updated_at' => $updated,
            ]);
        }

        $this->artisan('retention:prune')->assertSuccessful();

        $this->assertSame([$active, $new], $whatif->table('users')->orderBy('id')->pluck('id')->all());
        $this->assertSame([$active], $whatif->table('competitions')->pluck('wi_user')->all());
    }
    public function test_log_files_older_than_14_days_are_deleted(): void
    {
        $old = storage_path('logs/retention-test-old.log');
        $recent = storage_path('logs/retention-test-recent.log');
        file_put_contents($old, 'old');
        file_put_contents($recent, 'recent');
        touch($old, now()->subDays(15)->timestamp);

        try {
            $this->artisan('retention:prune')->assertSuccessful();

            $this->assertFileDoesNotExist($old);
            $this->assertFileExists($recent);
        } finally {
            @unlink($old);
            @unlink($recent);
        }
    }
}
