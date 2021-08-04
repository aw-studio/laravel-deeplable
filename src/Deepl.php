<?php

namespace AwStudio\Deeplable;

use Astrotomic\Translatable\Contracts\Translatable;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Deepl
{
    /**
     * Translate a model for a target language.
     *
     * @param  Model                         $model
     * @param  string                        $target_lang
     * @param  string|null                   $source_language
     * @return void
     * @throws InvalidCastException
     * @throws LazyLoadingViolationException
     * @throws LogicException
     * @throws BindingResolutionException
     * @throws GuzzleException
     * @throws MassAssignmentException
     */
    public function translateModel(Model $model, string $target_lang, string | null $source_language = null)
    {
        if (! $model instanceof Translatable) {
            throw new Exception("Translated models must implement the 'Astrotomic\Translatable\Contracts\Translatable' Contract.");
        }

        $translatedAttributes = collect($model->translatedAttributes);

        $translationArray = [];

        foreach ($translatedAttributes as $attribute) {
            $translationArray[$attribute] = $this->translate($model[$attribute], $target_lang, $source_language);
        }

        $model->update([
            $target_lang => $translationArray,
        ]);
    }

    /**
     * Translate a string to a target language with DeepL.
     *
     * @param  string                     $string
     * @param  string                     $target_lang
     * @param  string|null                $source_language
     * @return string
     * @throws BindingResolutionException
     * @throws GuzzleException
     */
    public function translate(string $string, string $target_lang, string | null $source_language = null): string
    {
        $endpoint = config('deeplable.api_url');
        $body = [
            'auth_key'        => config('deeplable.api_token'),
            'text'            => strip_tags($string, '<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<p>,<br>,<div>,<span>,<strong>,<b>'),
            'source_language' => strtoupper($source_language ?: config('translatable.fallback_locale')),
            'target_lang'     => strtoupper($target_lang),
        ];
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', $endpoint, ['query' => $body]);

        $content = json_decode($response->getBody(), true);

        $translation = $content['translations'][0]['text'];

        return $translation;
    }
}
