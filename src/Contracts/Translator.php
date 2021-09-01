<?php

namespace AwStudio\Deeplable\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Translator
{
    /**
     * Translate all translated attributes of a model.
     *
     * @param Model $model
     * @param array $attributes
     * @param  string                        $targetLang
     * @param  string|null                   $sourceLanguage
     * @return void
     */
    public function translate(Model $model, string $targetLang, string | null $sourceLanguage = null);

    /**
     * Translate a list of attributes.
     *
     * @param Model $model
     * @param array $attributes
     * @param  string                        $targetLang
     * @param  string|null                   $sourceLanguage
     * @return void
     */
    public function translateAttributes(Model $model, array $attributes, string $targetLang, string | null $sourceLanguage = null);
}
