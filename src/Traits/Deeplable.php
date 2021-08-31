<?php

namespace AwStudio\Deeplable\Traits;

use AwStudio\Deeplable\Facades\Deepl;

trait Deeplable
{
    /**
     * Translate the model to a target language.
     *
     * @param  string      $targetLang
     * @param  string|null $sourceLanguage
     * @return void
     */
    public function translateTo(string $targetLang, string | null $sourceLanguage = null)
    {
        Deepl::translateModel($this, $targetLang, $sourceLanguage);
    }

    /**
     * Translate a model attribute to a target language.
     *
     * @param  string      $attr
     * @param  string      $targetLang
     * @param  string|null $sourceLanguage
     * @return void
     */
    public function translateAttribute(string $attr, string $targetLang, string | null $sourceLanguage = null)
    {
        Deepl::translateModelAttribute($this, $attr, $targetLang, $sourceLanguage);
    }
}
