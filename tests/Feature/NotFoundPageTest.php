<?php

namespace Tests\Feature;

use App\Helpers\RouteHelpers;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    use RefreshDatabase;

    private function url(string $sub, string $path): string
    {
        return 'http://' . RouteHelpers::domainRemap($sub) . $path;
    }

    public function test_the_main_domain_links_to_explore_and_home(): void
    {
        $this->get(RouteHelpers::mainRoute('home') . '/no-such-page')
            ->assertNotFound()
            ->assertSee('Page not found')
            ->assertSee('Explore competitions')
            ->assertSee('href="' . RouteHelpers::mainRoute('explore') . '"', false);
    }

    public function test_app_links_back_to_the_management_home(): void
    {
        $this->actingAs(User::factory()->create())
            ->get($this->appUrl('/comps/0'))
            ->assertNotFound()
            ->assertSee('Your competitions')
            ->assertSee('href="' . route('manage.home') . '"', false);
    }

    public function test_the_digital_judge_links_back_to_its_landing_page(): void
    {
        $this->get($this->url('judge.', '/no/such/page'))
            ->assertNotFound()
            ->assertSee('Digital Judge')
            ->assertSee('href="' . route('dj.landing') . '"', false);
    }

    public function test_the_header_links_leave_the_subdomain_for_the_public_site(): void
    {
        $this->get($this->url('live.', '/no/such/page'))
            ->assertNotFound()
            ->assertSee('href="' . RouteHelpers::mainRoute('home') . '"', false)
            ->assertSee('href="' . RouteHelpers::mainRoute('explore') . '"', false);
    }

    public function test_an_inertia_visit_loads_it_as_a_full_page(): void
    {
        $url = $this->appUrl('/comps/0');

        $this->actingAs(User::factory()->create())
            // the current version, or Inertia's own version check would answer 409 too
            ->get($url, ['X-Inertia' => 'true', 'X-Inertia-Version' => (new HandleInertiaRequests)->version(request())])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', $url);
    }

    public function test_json_requests_still_get_json(): void
    {
        $this->getJson(RouteHelpers::mainRoute('home') . '/no-such-page')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }
}
