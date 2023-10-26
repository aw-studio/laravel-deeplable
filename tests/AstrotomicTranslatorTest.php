<?php

namespace Tests;

use Astrotomic\Translatable\TranslatableServiceProvider;
use AwStudio\Deeplable\Deepl;
use AwStudio\Deeplable\Translators\AstrotomicTranslator;
use Mockery;
use Tests\TestSupport\DummyPost;

class AstrotomicTranslatorTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            TranslatableServiceProvider::class,
        ];
    }

    public function testItTranslatesModelAttributes()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->andReturn('Hallo Welt');
        $translator = new AstrotomicTranslator($api);

        $post = new DummyPost([
            'en' => ['title' => 'Hello World'],
            'de' => ['title' => 'Foo'],
        ]);

        $translator->translateAttributes($post, ['title'], 'de', 'en');

        $this->app->setLocale('de');
        $this->assertSame($post->title, 'Hallo Welt');

        $this->app->setLocale('en');

        $post = new DummyPost([
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

        $post = new DummyPost([
            'en' => ['title' => 'Hello World'],
        ]);

        $this->assertEquals(['title'], $translator->getTranslatedAttributes($post, 'en'));
    }

    public function testAttributesAreNotOverritenWhenForceIsFalse()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->andReturn('Hallo Welt');
        $translator = new AstrotomicTranslator($api);

        $post = new DummyPost([
            'en' => ['title' => 'Hello World'],
            'de' => ['title' => 'Foo'],
        ]);
        $translator->translateAttributes($post, ['title'], 'de', 'en', false);

        $this->app->setLocale('de');
        $this->assertSame($post->title, 'Foo');

        $this->app->setLocale('en');
        $post = new DummyPost([
            'en' => ['title' => 'Hello World'],
            'de' => ['title' => 'Foo'],
        ]);
        $translator->translate($post, 'de', 'en', false);
        $this->app->setLocale('de');
        $this->assertSame($post->title, 'Foo');
    }
}
