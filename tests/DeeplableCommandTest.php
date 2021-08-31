<?php

namespace Tests;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use AwStudio\Deeplable\Deepl;
use AwStudio\Deeplable\DeeplableServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Orchestra\Testbench\TestCase;

class DeeplableCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Schema::create('posts', fn (Blueprint $table) => $table->id());
        Schema::create('post_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dummy_post_id');
            $table->string('locale');
            $table->string('title');
        });
    }

    public function tearDown(): void
    {
        Schema::drop('posts');
        Schema::drop('post_translations');
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            DeeplableServiceProvider::class,
        ];
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
}

class DummyPostTranslation extends Model
{
    public $table = 'post_translations';
    protected $fillable = ['title'];
    public $timestamps = false;
}

class DummyPost extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'posts';
    protected $translationModel = DummyPostTranslation::class;
    protected $translatedAttributes = ['title'];
    public $timestamps = false;
}
