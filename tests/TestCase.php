<?php

namespace Tests;

use App\Helpers\RouteHelpers;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * A path on the app. subdomain, where every signed-in page lives (routes/app.php).
     */
    protected function appUrl(string $path): string
    {
        return 'http://' . RouteHelpers::domainRemap('app.') . $path;
    }
}
