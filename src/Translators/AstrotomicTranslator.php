<?php

namespace AwStudio\Deeplable\Translators;

use Illuminate\Database\Eloquent\Model;

class AstrotomicTranslator extends BaseTranslator
{
    /**
     * Translate the given model attribute.
     *
     * @param Model $model
     * @param string $attribute
     * @param string $locale
     * @param string $translation
     * @return void
     */
    protected function translateAttribute(Model $model, $attribute, $locale, $translation)
    {
        $model->translateOrNew($locale)->setAttribute($attribute, $translation);
    }

    /**
     * Get a list of the translated attributes of a model.
     *
     * @param Model $model
     * @param string $locale
     * @return array
     */
    public function getTranslatedAttributes(Model $model, $locale)
    {
        return array_keys($model->getTranslationsArray()[$locale] ?? []);
    }

    /**
     * Translate all translated attributes of a model.
     *
     * @param Model $model
     * @param array $attributes
     * @param  string                        $targetLang
     * @param  string|null                   $sourceLanguage
     * @return void
     */
    public function translate(Model $model, string $targetLang, string | null $sourceLanguage = null)
    {
        if (! $model->hasTranslation($sourceLanguage)) {
            return;
        }

        return parent::translate($model, $targetLang, $sourceLanguage);
    }
}
