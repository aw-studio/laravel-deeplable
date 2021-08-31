<?php

namespace Tests;

use AwStudio\Deeplable\Deepl;
use AwStudio\Deeplable\Translators\BaseTranslator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\AssertableJsonString;
use Mockery;
use PHPUnit\Framework\TestCase;

class BaseTranslatorTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testTranslateAttributesCallsTranslateAttribute()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->twice()->andReturn('bar');
        $post = Mockery::mock(Model::class);
        $post->shouldReceive('getAttribute')->andReturn('foo');
        $post->shouldReceive('jsonSerialize');
        $translator = new DummyTranslatorMock($api);
        $translator->translateAttributes($post, ['title', 'text'], 'de', 'en');

        $this->assertSame($translator->calledTimes, 2);
        $translator->calledParams[0]->assertFragment([
            'attribute' => 'title',
            'locale' => 'de',
            'translation' => 'bar',
        ]);
        $translator->calledParams[1]->assertFragment([
            'attribute' => 'text',
            'locale' => 'de',
            'translation' => 'bar',
        ]);
    }

    public function testTranslateAttributesCallsApi()
    {
        $api = Mockery::mock(Deepl::class);
        $api->shouldReceive('translate')->once()->withArgs([
            'foo', 'de', 'en',
        ]);
        $post = Mockery::mock(Model::class);
        $post->shouldReceive('getAttribute')->withArgs(['title'])->andReturn('foo');
        $translator = new DummyTranslatorMock($api);
        $translator->translateAttributes($post, ['title'], 'de', 'en');
    }
}

class DummyTranslatorMock extends BaseTranslator
{
    public $calledTimes = 0;
    public $calledParams = [];

    public function getTranslatedAttributes(Model $model, $locale)
    {
        return ['title', 'text'];
    }

    protected function translateAttribute(Model $model, $attribute, $locale, $translation)
    {
        $this->calledTimes++;
        $this->calledParams [] = new AssertableJsonString([
            'model' => $model,
            'attribute' => $attribute,
            'locale' => $locale,
            'translation' => $translation,
        ]);
    }
}
