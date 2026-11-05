<?php

namespace Tests\Feature\DigitalJudge;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JudgePwaTest extends TestCase
{
    use RefreshDatabase;

    private const MANIFEST_LINK = '<link rel="manifest" href="/judge.webmanifest">';

    public function test_judge_pages_link_the_manifest(): void
    {
        $this->get(route('judge.login'))
            ->assertOk()
            ->assertSee(self::MANIFEST_LINK, false);
    }

    public function test_management_pages_do_not_link_the_manifest(): void
    {
        $this->get($this->appUrl('/login'))
            ->assertOk()
            ->assertDontSee(self::MANIFEST_LINK, false);
    }

    public function test_the_manifest_opens_the_judge_app_and_its_icons_exist(): void
    {
        $manifest = json_decode(file_get_contents(public_path('judge.webmanifest')), true);

        // No trailing slash: the judge home route is /v2, and /v2/ wouldn't include it
        $this->assertSame('/v2', $manifest['start_url']);
        $this->assertSame('/v2', $manifest['scope']);
        $this->assertSame('/v2', parse_url(route('judge.index'), PHP_URL_PATH));

        $this->assertNotEmpty($manifest['icons']);
        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path($icon['src']));
        }
    }

    public function test_the_service_worker_and_offline_page_exist(): void
    {
        $this->assertFileExists(public_path('judge-sw.js'));
        $this->assertFileExists(public_path('judge-offline.html'));
    }
}
