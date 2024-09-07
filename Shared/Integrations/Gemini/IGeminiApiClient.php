<?php

namespace Shared\Integrations\Gemini;

interface IGeminiApiClient
{
    public function generateText(string $prompt): IGenerateTextResponse;
}