<?php

namespace AwStudio\Deeplable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string translate(stirng $string, string $targetLang, string | null $sourceLanguage = null)
 */
class Deepl extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'deeplable.api';
    }
}
