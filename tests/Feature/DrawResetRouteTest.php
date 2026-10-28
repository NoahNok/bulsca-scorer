<?php

namespace Tests\Feature;

use Tests\TestCase;

class DrawResetRouteTest extends TestCase
{
    public function test_draw_reset_route_requires_post(): void
    {
        $route = app('router')->getRoutes()->getByName('manage.competitions.heats_and_draws.draws.regenerate');

        $this->assertNotNull($route);
        $this->assertContains('POST', $route->methods());
        $this->assertNotContains('GET', $route->methods());
    }
}
