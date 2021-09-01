<?php

namespace Tests;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\TranslatableServiceProvider;
use AwStudio\Deeplable\Deepl;
use AwStudio\Deeplable\Translators\AstrotomicTranslator;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Orchestra\Testbench\TestCase;

class AstrotomicTranslatorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            TranslatableServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $config = $app['config'];
        $config->set('translatable.fallback_locale', 'en');
        $config->set('translatable.locales', [
            'en', 'de',
        ]);
    }

    public function testItTranslatesModelAttributes()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->andReturn('Hallo Welt');
        $translator = new AstrotomicTranslator($api);

        $post = new DummyTranslatablePost([
            'en' => ['title' => 'Hello World'],
            'de' => ['title' => 'Foo'],
        ]);

        $translator->translateAttributes($post, ['title'], 'de', 'en');

        $this->app->setLocale('de');
        $this->assertSame($post->title, 'Hallo Welt');

        $this->app->setLocale('en');

        $post = new DummyTranslatablePost([
            'en' => ['title' => 'Hello World'],
            'de' => ['title' => 'Foo'],
        ]);

        $translator->translate($post, 'de', 'en');

        $this->app->setLocale('de');
        $this->assertSame($post->title, 'Hallo Welt');
    }

    public function testItGetsTranslatedAttributes()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->andReturn('Hallo Welt');
        $translator = new AstrotomicTranslator($api);

        $post = new DummyTranslatablePost([
            'en' => ['title' => 'Hello World'],
        ]);

        $this->assertEquals(['title'], $translator->getTranslatedAttributes($post, 'en'));
    }

    public function testAttributesAreNotOverritenWhenForceIsFalse()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->andReturn('Hallo Welt');
        $translator = new AstrotomicTranslator($api);

        $post = new DummyTranslatablePost([
            'en' => ['title' => 'Hello World'],
            'de' => ['title' => 'Foo'],
        ]);
        $translator->translateAttributes($post, ['title'], 'de', 'en', false);

        $this->app->setLocale('de');
        $this->assertSame($post->title, 'Foo');

        $this->app->setLocale('en');
        $post = new DummyTranslatablePost([
            'en' => ['title' => 'Hello World'],
            'de' => ['title' => 'Foo'],
        ]);
        $translator->translate($post, 'de', 'en', false);
        $this->app->setLocale('de');
        $this->assertSame($post->title, 'Foo');
    }
}

class DummyTranslatablePostTranslation extends Model
{
    public $table = 'post_translations';
    protected $fillable = ['title'];
    public $timestamps = false;
}

class DummyTranslatablePost extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'posts';
    protected $translationModel = DummyTranslatablePostTranslation::class;
    protected $translatedAttributes = ['title'];
    public $timestamps = false;
}
