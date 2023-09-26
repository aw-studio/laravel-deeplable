<?php

namespace AwStudio\Deeplable\Translators;

use Illuminate\Database\Eloquent\Model;

class AstrotomicTranslator extends BaseTranslator
{
    /**
     * Translate the given model attribute.
     *
     * @param  Model  $model
     * @param  string  $attribute
     * @param  string  $locale
     * @param  string  $translation
     * @param  bool  $force
     * @return void
     */
    protected function translateAttribute(Model $model, $attribute, $locale, $translation, bool $force = true)
    {
        $translationModel = $model->translateOrNew($locale);
        
        // Ignore attribute when it has a value and it should not be overriten.
        if (! $force && $translationModel->getAttribute($attribute)) {
            return;
        }
        
        $translationModel->setAttribute($attribute, $translation);
        $translationModel->setAttribute($model->getTranslationRelationKey(), $model->getKey());

        $translationModel->save();
        $model->fresh()->update(['updated_at' => now()]);
    }

    /**
     * Get a list of the translated attributes of a model.
     *
     * @param  Model  $model
     * @param  string  $locale
     * @return array
     */
    public function getTranslatedAttributes(Model $model, $locale)
    {
        return array_keys($model->getTranslationsArray()[$locale] ?? []);
    }

    /**
     * Translate all translated attributes of a model.
     *
     * @param  Model  $model
     * @param  array  $attributes
     * @param  string  $targetLang
     * @param  string|null  $sourceLanguage
     * @param  bool  $force
     * @return void
     */
    public function translate(Model $model, string $targetLang, string|null $sourceLanguage = null, bool $force = true)
    {
        if (! $model->hasTranslation($sourceLanguage)) {
            return;
        }

        return parent::translate($model, $targetLang, $sourceLanguage, $force);
    }
}
