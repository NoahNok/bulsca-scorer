<?php

namespace Tests\Feature;

use App\Helpers\RouteHelpers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The Management app is its own Inertia app: its own root view and shared props,
 * separate from the Digital Judge. See docs/adr/0002-public-blade-management-svelte.md.
 */
class ManagementAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_sign_in(): void
    {
        $this->get(route('manage.home'))->assertRedirect(route('login'));
    }

    public function test_organisers_get_the_management_app(): void
    {
        $organiser = User::factory()->create();

        $this->actingAs($organiser)
            ->get(route('manage.home'))
            ->assertOk()
            ->assertViewIs('manage')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Manage/Home')
                ->where('manage.isAdmin', false)
                ->missing('judge'));
    }

    public function test_admins_are_flagged_for_the_admin_link(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['admin' => true])->save();

        $this->actingAs($admin)
            ->get(route('manage.home'))
            ->assertInertia(fn (Assert $page) => $page->where('manage.isAdmin', true));
    }

    public function test_the_old_dashboard_sends_you_to_the_management_home(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->appUrl('/dashboard'))
            ->assertStatus(301)
            ->assertRedirect(route('manage.home'));
    }

    public function test_the_management_home_is_the_app_root(): void
    {
        $this->assertSame($this->appUrl('/'), route('manage.home') . '/');

        $this->actingAs(User::factory()->create())
            ->get($this->appUrl('/'))
            ->assertInertia(fn (Assert $page) => $page->component('Manage/Home'));
    }

    public function test_old_main_domain_links_are_sent_to_app(): void
    {
        $this->get('/comps/5/events?tab=sercs')
            ->assertStatus(301)
            ->assertRedirect($this->appUrl('/comps/5/events?tab=sercs'));

        $this->get('/login')->assertRedirect($this->appUrl('/login'));
        $this->get('/admin/users')->assertRedirect($this->appUrl('/admin/users'));
    }

    public function test_the_main_domain_home_is_the_public_landing_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertViewIs('welcome');
    }

    public function test_signing_out_returns_to_the_public_site(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('logout'))
            ->assertRedirect(RouteHelpers::mainRoute('home'));

        $this->assertGuest();
    }

    public function test_officials_only_get_the_digital_judge(): void
    {
        $official = User::factory()->create();
        $official->forceFill(['judge_only' => true])->save();

        $this->actingAs($official)
            ->get(route('manage.home'))
            ->assertRedirectContains('judge.');
    }
}
