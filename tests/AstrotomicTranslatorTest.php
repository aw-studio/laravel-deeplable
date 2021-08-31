<?php

namespace Tests;

use Mockery;
use AwStudio\Deeplable\Deepl;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Testing\AssertableJsonString;
use AwStudio\Deeplable\Translators\BaseTranslator;
use Astrotomic\Translatable\TranslatableServiceProvider;
use AwStudio\Deeplable\Translators\AstrotomicTranslator;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class AstrotomicTranslatorTest extends TestCase
{
    public function setUp():void
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
            'en' => ['title' => 'Hello World']
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
            'en' => ['title' => 'Hello World']
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
