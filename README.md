# Laravel Deeplable

An extension package for https://github.com/Astrotomic/laravel-translatable which allows you to translate your model attribtutes from the default language to a target language with DeepL.


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
You May also configure all Models you want to translate when you run the `deeplable:run` command.

## Usage

You can translate your translatable models to all languages that are set in your `config/translatable.php` as long as they are available on DeepL. In order to translate your models to a target language the default translation (`fallback_locale`) must be available. You can use the `Deeplable` trait to easily translate your model to a target language.

```php
use AwStudio\Deeplable\Traits\Deeplable;

class Post extends Model implements TranslatableContract
{
    use Translatable, Deeplable;
}
```

Imagine you have stored the default language ('en') and want to auto-generate the german translation for all translated attributes:

```php
$post = Post::first();

$post->translateTo('de'); 
```

If you want to translate all Models to all locales, simply run the `deeplable` artisan command:

```bash
php artisan deeplable:run
```

You may also set an argument if you want to translate a specific language:

```bash
php artisan deeplable:run fr
```
