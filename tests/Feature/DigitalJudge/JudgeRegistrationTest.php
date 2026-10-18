<?php

namespace Tests\Feature\DigitalJudge;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class JudgeRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'correct-horse-battery-staple';

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    private function register(string $email, string $name, string $password = self::PASSWORD)
    {
        return $this->post(route('judge.register.post'), [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
    }

    private function enterPin(User $user)
    {
        $pin = $user->createOneTimePassword()->password;

        return $this->post(route('judge.login.post'), ['email' => $user->email, 'step' => 'pin', 'pin' => $pin]);
    }

    private function officialAddedByAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Jo Official',
            'email' => 'jo@example.com',
            'judge_only' => true,
            'email_verified_at' => null,
        ]);
    }

    public function test_registering_a_new_account_saves_the_password_once_the_pin_is_entered(): void
    {
        $this->register('new@example.com', 'New Judge')->assertOk();

        $judge = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertFalse(Hash::check(self::PASSWORD, $judge->password));

        $this->enterPin($judge)->assertRedirect();

        $judge->refresh();
        $this->assertTrue($judge->hasVerifiedEmail());
        $this->assertSame('New Judge', $judge->name);
        $this->assertTrue(Hash::check(self::PASSWORD, $judge->password));
    }

    public function test_an_official_can_claim_their_account_with_the_pin(): void
    {
        $official = $this->officialAddedByAdmin();

        $this->register('jo@example.com', 'Jo Claimed')->assertOk();
        $this->enterPin($official)->assertRedirect();

        $official->refresh();
        $this->assertSame('Jo Claimed', $official->name);
        $this->assertTrue(Hash::check(self::PASSWORD, $official->password));
    }

    public function test_registering_someone_elses_account_does_not_set_its_password(): void
    {
        $official = $this->officialAddedByAdmin();
        $originalPassword = $official->password;

        // the attacker registers with the official's email but never gets the PIN
        $this->register('jo@example.com', 'Attacker')->assertOk();

        $official->refresh();
        $this->assertSame('Jo Official', $official->name);
        $this->assertSame($originalPassword, $official->password);

        // the real official signs in with their PIN from a different browser
        $this->flushSession();
        $this->enterPin($official)->assertRedirect();
        $this->post(route('judge.logout'));

        $official->refresh();
        $this->assertTrue($official->hasVerifiedEmail());
        $this->assertSame('Jo Official', $official->name);
        $this->assertFalse(Hash::check(self::PASSWORD, $official->password));

        $this->flushSession();
        $this->post(route('judge.login.password'), ['email' => 'jo@example.com', 'password' => self::PASSWORD]);
        $this->assertGuest();
    }
}
