<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // tests wipe the database, so never let them near a real one (e.g. if phpunit.xml's env is ignored)
        foreach (['mysql', 'whatif'] as $connection) {
            $database = $app['config']->get("database.connections.{$connection}.database");

            if (!is_string($database) || !str_ends_with($database, '_test')) {
                throw new \RuntimeException("Refusing to run tests against the '{$database}' database. Test databases must end in _test.");
            }
        }

        return $app;
    }
}
