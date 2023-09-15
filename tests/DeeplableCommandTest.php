<?php

namespace Tests;

use AwStudio\Deeplable\Deepl;
use AwStudio\Deeplable\DeeplableServiceProvider;
use Mockery;
use Tests\TestSupport\DummyPost;

class DeeplableCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DeeplableServiceProvider::class,
        ];
    }

    public function test_deeplable_run_command()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->andReturn('bar');

        $this->app->singleton('deeplable.api', function () use ($api) {
            return $api;
        });

        DummyPost::create(['en' => ['title' => 'foo']]);
        DummyPost::create(['en' => ['title' => 'foo']]);
        DummyPost::create(['en' => ['title' => 'foo']]);

        $this->app->setLocale('de');
        foreach (DummyPost::all() as $post) {
            $this->assertSame(null, $post->title);
        }

        $this->app->setLocale('en');
        $this->artisan('deeplable:run', ['locale' => 'de']);

        $this->app->setLocale('de');
        foreach (DummyPost::all() as $post) {
            $this->assertSame('bar', $post->title);
        }
    }

    public function test_deeplable_run_command_with_force_option()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->andReturn('bar');

        $this->app->singleton('deeplable.api', function () use ($api) {
            return $api;
        });

        DummyPost::create([
            'en' => ['title' => 'foo'],
            'de' => ['title' => 'baz'],
        ]);
        DummyPost::create([
            'en' => ['title' => 'foo'],
            'de' => ['title' => 'baz'],
        ]);

        // Assuming force is disabled by default.
        $this->app->setLocale('en');
        $this->artisan('deeplable:run', ['locale' => 'de']);
        $this->app->setLocale('de');
        foreach (DummyPost::all() as $post) {
            $this->assertSame('baz', $post->title);
        }

        $this->app->setLocale('en');
        $this->artisan('deeplable:run', ['locale' => 'de', '--force' => false]);
        $this->app->setLocale('de');
        foreach (DummyPost::all() as $post) {
            $this->assertSame('baz', $post->title);
        }

        $this->app->setLocale('en');
        $this->artisan('deeplable:run', ['locale' => 'de', '--force' => true]);
        $this->app->setLocale('de');
        foreach (DummyPost::all() as $post) {
            $this->assertSame('bar', $post->title);
        }
    }
}
