<?php

namespace AwStudio\Deeplable\Facades;

use AwStudio\Deeplable\Contracts\Translator as TranslatorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $alias, \Closure $resolver)
 * @method static TranslatorContract get(string $alias)
 * @method static TranslatorContract for(Model $model)
 * @method static void strategy(\Closure $closure)
 */
class Translator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'deeplable.translator';
    }
}
