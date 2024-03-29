<?php

namespace AwStudio\Deeplable\Translators;

use AwStudio\Deeplable\Contracts\Translator;
use AwStudio\Deeplable\Deepl;
use Illuminate\Database\Eloquent\Model;

abstract class BaseTranslator implements Translator
{
    /**
     * Translate the given model attribute.
     *
     * @param  Model  $model
     * @param  string  $attribute
     * @param  string  $locale
     * @param  string  $translation
     * @return void
     */
    abstract protected function translateAttribute(Model $model, $attribute, $locale, $translation, bool $force = true);

    /**
     * Get a list of the translated attributes of a model.
     *
     * @param  Model  $model
     * @param  string  $locale
     * @return array
     */
    abstract public function getTranslatedAttributes(Model $model, $locale);

    /**
     * Create new Translator instance.
     *
     * @param  Deepl  $api
     * @return void
     */
    public function __construct(
        protected Deepl $api
    ) {
        //
    }

    /**
     * Translate all translated attributes of a model.
     *
     * @param  Model  $model
     * @param  array  $attributes
     * @param  string  $targetLang
     * @param  string|null  $sourceLanguage
     * @return void
     */
    public function translate(Model $model, string $targetLang, string|null $sourceLanguage = null, bool $force = true)
    {
        $translatedAttributes = $this->getTranslatedAttributes($model, $sourceLanguage);

        if (empty($translatedAttributes)) {
            return;
        }

        $this->translateAttributes(
            $model,
            $translatedAttributes,
            $targetLang,
            $sourceLanguage,
            $force
        );
    }

    /**
     * Translate a list of attributes.
     *
     * @param  Model  $model
     * @param  array  $attributes
     * @param  string  $targetLang
     * @param  string|null  $sourceLanguage
     * @return void
     */
    public function translateAttributes(Model $model, array $attributes, string $targetLang, string|null $sourceLanguage = null, bool $force = true)
    {
        foreach ($attributes as $attribute) {
            $translation = $this->api->translate(
                (string) $model->getAttribute($attribute),
                $targetLang,
                $sourceLanguage
            );

            $this->translateAttribute($model, $attribute, $targetLang, $translation, $force);
        }
    }
}
