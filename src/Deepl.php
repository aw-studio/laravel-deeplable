<?php

namespace AwStudio\Deeplable;

use GuzzleHttp\Client;

/**
 * DeepL V2 Api adapter.
 */
class Deepl
{
    /**
     * Guzzle http client.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Create new Deepl instance.
     *
     * @param  string  $apiToken
     * @param  string  $apiUrl
     * @param  string  $fallbackLocale
     */
    public function __construct(
        protected string $apiToken,
        protected string $apiUrl = 'https://api-free.deepl.com/v2',
        protected string $fallbackLocale = 'en'
    ) {
        $this->client = new Client();
    }

    /**
     * Translate a string to a target language with DeepL.
     *
     * @param  string  $string
     * @param  string  $targetLang
     * @param  string|null  $sourceLanguage
     * @return string
     */
    public function translate(string $string, string $targetLang, string|null $sourceLanguage = null): string
    {
        // Avoid translating empty strings.
        if (! $string) {
            return '';
        }
        if ($this->isJson($string)) {
            return $string;
        }

        if (str_contains($this->apiUrl, '-free')) {
            $string = strip_tags($string);
        }

        $body = array_merge(
            [
                'auth_key'        => $this->apiToken,
                'text'            => $string,
                'source_language' => strtoupper($sourceLanguage ?: $this->fallbackLocale),
                'target_lang'     => strtoupper($targetLang),
            ],
            config('deeplable.api_params')
        );

        $content = $this->apiCall('POST', 'translate', [
            'query' => $body,
        ]);

        $translation = $content['translations'][0]['text'];

        return $translation;
    }

    /**
     * Send a deepl api call.
     *
     * @param  string  $method
     * @param  string  $action
     * @param  array  $params
     * @return array
     */
    protected function apiCall($method, $action, $params = []): array
    {
        $response = $this->client->request(
            $method,
            $this->apiUrl.'/'.$action,
            $params
        );

        return json_decode($response->getBody(), true);
    }

    public function isJson($string)
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
