<?php

namespace AwStudio\Deeplable;

use Astrotomic\Translatable\Contracts\Translatable;
use Exception;
use Illuminate\Database\Eloquent\InvalidCastException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\LazyLoadingViolationException;
use LogicException;

class Deepl
{
    /**
     * Translate a model to a target language.
     *
     * @param  Model                         $model
     * @param  string                        $targetLang
     * @param  string|null                   $sourceLanguage
     * @return void
     * @throws InvalidCastException
     * @throws LazyLoadingViolationException
     * @throws LogicException
     * @throws BindingResolutionException
     * @throws GuzzleException
     * @throws MassAssignmentException
     */
    public function translateModel(Model $model, string $targetLang, string | null $sourceLanguage = null)
    {
        if (! $model instanceof Translatable) {
            throw new Exception("Translated models must implement the 'Astrotomic\Translatable\Contracts\Translatable' Contract.");
        }

        $translatedAttributes = collect($model->translatedAttributes);

        $translationArray = [];

        foreach ($translatedAttributes as $attribute) {
            if (! $model[$attribute]) {
                continue;
            }
            $translationArray[$attribute] = $this->translate($model[$attribute], $targetLang, $sourceLanguage);
        }

        $model->update([
            $targetLang => $translationArray,
        ]);
    }

    /**
     * Translate a model attribute to a target language.
     *
     * @param  Model                         $model
     * @param  string                        $attr
     * @param  string                        $targetLang
     * @param  string|null                   $sourceLanguage
     * @return void
     * @throws Exception
     * @throws InvalidCastException
     * @throws LazyLoadingViolationException
     * @throws LogicException
     * @throws MassAssignmentException
     */
    public function translateModelAttribute(Model $model, string $attr, string $targetLang, string | null $sourceLanguage = null)
    {
        if (! $model instanceof Translatable) {
            throw new Exception("Translated models must implement the 'Astrotomic\Translatable\Contracts\Translatable' Contract.");
        }

        if (! $model[$attr]) {
            return;
        }

        $model->update([
            $targetLang => [
                $attr => $this->translate($model[$attr], $targetLang, $sourceLanguage),
            ],
        ]);
    }

    /**
     * Translate a string to a target language with DeepL.
     *
     * @param  string                     $string
     * @param  string                     $targetLang
     * @param  string|null                $sourceLanguage
     * @return string
     * @throws BindingResolutionException
     * @throws GuzzleException
     */
    public function translate(string $string, string $targetLang, string | null $sourceLanguage = null): string
    {
        $endpoint = config('deeplable.api_url');
        $body = [
            'auth_key'        => config('deeplable.api_token'),
            'text'            => strip_tags($string, '<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<p>,<br>,<div>,<span>,<strong>,<b>'),
            'source_language' => strtoupper($sourceLanguage ?: config('translatable.fallback_locale')),
            'target_lang'     => strtoupper($targetLang),
        ];
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', $endpoint, ['query' => $body]);

        $content = json_decode($response->getBody(), true);

        $translation = $content['translations'][0]['text'];

        return $translation;
    }
}
