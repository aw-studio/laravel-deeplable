<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\TestSupport\DummyPost;

class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/TestSupport/database/migrations');

        $this->artisan('migrate', [
            '--database' => 'testbench',
        ])->run();
    }

    public function tearDown(): void
    {
        $this->artisan('migrate:reset', [
            '--database' => 'testbench',
        ])->run();

        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app)
    {
        $config = $app['config'];
        $config->set('deeplable.api_url', '');
        $config->set('deeplable.api_token', '');
        $config->set('deeplable.translated_models', [
            DummyPost::class,
        ]);
        $config->set('translatable.fallback_locale', 'en');
        $config->set('translatable.locales', [
            'en', 'de',
        ]);

        $config->set('database.default', 'testbench');
        $config->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
