# Laravel Deeplable

A package for translating Laravel Models using DeepL's API. It contains a translator for the [astrotomic/laravel-translatable](https://github.com/Astrotomic/laravel-translatable) composer package. Translators for other usecases can be written easily.

## Installation

Install the package via composer:

```bash
composer require aw-studio/laravel-deeplable
```

and publish the config file:

```bash
php artisan vendor:publish --tag=deeplable --force
```

## Setup

After publishing the `config/deeplable.php` you must enter your DeepL-Secret in order to use their API.
You may also configure all Models you want to translate when you run the `deeplable:run` command.

## Usage

You can translate your translatable models to all languages that are set in your `config/translatable.php` as long as they are available on DeepL. In order to translate your models to a target language the default translation (`fallback_locale`) must be available. You can use the `Deeplable` trait to easily translate your model to a target language.

```php
use AwStudio\Deeplable\Traits\Deeplable;

class Post extends Model
{
    use Deeplable;
}
```

Imagine you have stored the default language ('en') and want to auto-generate the german translation for all translated attributes:

```php
$post = Post::first();

$post->translateTo('de');
$post->translateAttributeTo('de', 'title');
$post->translateAttributesTo('de', ['title', 'text']);
```

If you want to translate all Models to all locales, simply run the `deeplable` artisan command:

```bash
php artisan deeplable:run
```

You may also set an argument if you want to translate a specific language:

```bash
php artisan deeplable:run fr
```

### Translators

Translators have the purpose to bind a translatable attribute to a model. A build in example is the `AstrotomicTranslator` that updates models that are translated by the [astrotomic/laravel-translatable](https://github.com/Astrotomic/laravel-translatable) package.

#### Creating A Translator

A translator must extend the `AwStudio\Deeplable\Translators\BaseTranslator` class which has 2 abstract methods:

```php
class AstrotomicTranslator
{
    protected function translateAttribute(Model $model, $attribute, $locale, $translation)
    {
        $model->translateOrNew($locale)->setAttribute($attribute, $translation);
    }

    public function getTranslatedAttributes(Model $model, $locale)
    {
        return array_keys($model->getTranslationsArray()[$locale] ?? []);
    }
}
```

#### Registering The Translator

A translator may be registered in a Service Provider like this:

```php
use AwStudio\Deeplable\Translators\Resolver;
use Astrotomic\Translatable\Contracts\Translatable;

public function register()
{
    Translator::register(Translatable::class, function () {
            return new AstrotomicTranslator($this->app['deeplable.api']);
    });
}
```

#### Using The Translator

The translator can then be used like this:

```php
use AwStudio\Deeplable\Facades\Translator;
use Astrotomic\Translatable\Contracts\Translatable;

Translator::get(Translatable::class)->translate($post, 'de', 'en'); // Translates the whole model from en to de
Translator::get(Translatable::class)->translateAttributes($post, ['title', 'text'], 'de', 'en'); // Translates attributes title and text from en to de
```

### Translation Strategy

Given the case that different models need different translators, a translation strategy can be specified in a service provider like this:

```php
use AwStudio\Deeplable\Facades\Translator;
use AwStudio\Deeplable\Translators\Resolver;
use Astrotomic\Translatable\Contracts\Translatable;

public function register()
{
    Translator::strategy(function(Model $model) {
        if($model instanceof Translatable) {
            return Translatable::class;
        }
        // something else...
    });
}
```

The correct translator can now be received like this:

```php
Translator::for($post)->translate(...);
```
