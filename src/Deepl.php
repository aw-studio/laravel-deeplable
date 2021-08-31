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
     * @param string $apiToken
     * @return void
     */
    public function __construct(
        protected string $apiToken,
        protected string $apiUrl = 'https://api-free.deepl.com/v2/translate',
        protected string $fallbackLocale = 'en'
    ) {
        $this->client = new Client();
    }

    /**
     * Translate a string to a target language with DeepL.
     *
     * @param  string                     $string
     * @param  string                     $targetLang
     * @param  string|null                $sourceLanguage
     * @return string
     */
    public function translate(string $string, string $targetLang, string | null $sourceLanguage = null): string
    {
        // Avoid translating empty strings.
        if (! $string) {
            return '';
        }

        $body = [
            'auth_key'        => $this->apiToken,
            'text'            => strip_tags($string, '<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<p>,<br>,<div>,<span>,<strong>,<b>'),
            'source_language' => strtoupper($sourceLanguage ?: $this->fallbackLocale),
            'target_lang'     => strtoupper($targetLang),
        ];

        $content = $this->apiCall('POST', 'translate', [
            'query' => $body,
        ]);

        $translation = $content['translations'][0]['text'];

        return $translation;
    }

    /**
     * Send a deepl api call.
     *
     * @param string $method
     * @param string $action
     * @param array $params
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
}
