<?php

namespace AwStudio\Deeplable\Traits;

use AwStudio\Deeplable\Facades\Deepl;

trait Deeplable
{
    /**
     * Translate the model to a target language.
     *
     * @param  string      $target_lang
     * @param  string|null $source_language
     * @return void
     */
    public function translateTo(string $target_lang, string | null $source_language = null)
    {
        Deepl::translateModel($this, $target_lang, $source_language);
    }
}
