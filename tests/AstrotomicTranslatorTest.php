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

    public function testItTranslatesModelAttributes()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->andReturn('Hallo Welt');
        $translator = new AstrotomicTranslator($api);

        $post = new DummyTranslatablePost([
            'en' => ['title' => 'Hello World'],
        ]);

        $translator->translateAttributes($post, ['title'], 'de', 'en');

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
