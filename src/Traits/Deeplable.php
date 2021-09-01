<?php

namespace AwStudio\Deeplable\Traits;

use AwStudio\Deeplable\Facades\Translator;

trait Deeplable
{
    /**
     * Translate the model to a target language.
     *
     * @param  string      $targetLang
     * @param  string|null $sourceLanguage
     * @param bool $force
     * @return void
     */
    public function translateTo(string $targetLang, string | null $sourceLanguage = null, bool $force = true)
    {
        Translator::for($this)->translate($this, $targetLang, $sourceLanguage, $force);

        $this->save();
    }

    /**
     * Translate a model attribute to a target language.
     *
     * @param  string      $attr
     * @param  string      $targetLang
     * @param  string|null $sourceLanguage
     * @param bool $force
     * @return void
     */
    public function translateAttributeTo(string $attr, string $targetLang, string | null $sourceLanguage = null, bool $force = true)
    {
        $this->translateAttributesTo([$attr], $targetLang, $sourceLanguage, $force);
    }

    /**
     * Translate multiple model attributes to a target language.
     *
     * @param  string      $attr
     * @param  string      $targetLang
     * @param  string|null $sourceLanguage
     * @param bool $force
     * @return void
     */
    public function translateAttributesTo(array $attributes, string $targetLang, string | null $sourceLanguage = null, bool $force = true)
    {
        Translator::for($this)->translateAttributes($this, $attributes, $targetLang, $sourceLanguage);

        $this->save();
    }
}
