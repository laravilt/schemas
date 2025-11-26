<?php

namespace Laravilt\Schemas\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{

    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup if needed
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Laravilt\Schemas\SchemasServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup environment for testing
        config()->set('database.default', 'testing');
    }
}
