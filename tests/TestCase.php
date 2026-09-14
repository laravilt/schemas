<?php

namespace Laravilt\Schemas\Tests;

use Laravilt\Schemas\SchemasServiceProvider;
use Laravilt\Support\SupportServiceProvider;
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
            SupportServiceProvider::class,
            SchemasServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup environment for testing
        config()->set('database.default', 'testing');
    }
}
