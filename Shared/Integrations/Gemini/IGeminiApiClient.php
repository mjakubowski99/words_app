<?php

declare(strict_types=1);

namespace Shared\Integrations\Gemini;

interface IGeminiApiClient
{
    public function generateText(string $prompt): IGenerateTextResponse;
}
