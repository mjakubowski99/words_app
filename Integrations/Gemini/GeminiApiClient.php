<?php

namespace Integrations\Gemini;

use Illuminate\Support\Arr;
use Shared\Integrations\Gemini\IGeminiApiClient;
use Shared\Integrations\Gemini\IGenerateTextResponse;
use Shared\Utils\Config\IConfig;
use Illuminate\Support\Facades\Http;

class GeminiApiClient implements IGeminiApiClient
{
    public function __construct(private readonly IConfig $config) {}

    public function generateText(string $prompt): IGenerateTextResponse
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->buildUrl(), [
            'contents' => [
                'parts' => [
                    ['text' => $prompt],
                ],
            ],
        ]);

        return new GenerateTextResponse($response);
    }

    private function buildUrl(): string
    {
        $url = $this->config->get('gemini.api_url');
        $key = $this->config->get('gemini.api_key');
        $endpoint = $this->config->get('gemini.endpoints.generate_text');

        return rtrim($url, '/') . '/' . ltrim($endpoint, '/'). "?" . Arr::query([
           'key' => $key,
        ]);
    }
}