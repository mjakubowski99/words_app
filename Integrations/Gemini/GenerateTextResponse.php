<?php

namespace Integrations\Gemini;

use Illuminate\Http\Client\Response;
use Shared\Integrations\Gemini\IGenerateTextResponse;

class GenerateTextResponse implements IGenerateTextResponse
{
    public function __construct(private Response $response)
    {

    }
    public function success(): bool
    {
        return $this->response->successful();
    }

    public function getStatusCode(): int
    {
        return $this->response->status();
    }

    public function getPromptTokensCount(): int
    {
        return $this->response->json()['usageMetadata']['promptTokenCount'];
    }

    public function getResponseTokenCount(): int
    {
        return $this->response->json()['usageMetadata']['candidatesTokenCount'];
    }

    public function getSuccessResponse(): ?array
    {
        return $this->success() ? $this->response->json() : null;
    }

    public function getGeneratedContent(): ?string
    {
        return $this->success() ? $this->response->json()['candidates'][0]['content']['parts'][0]['text'] : null;
    }

    public function getErrorReason(): ?string
    {
        return null;
    }

    public function getErrorResponse(): ?array
    {
        return !$this->success() ? $this->response->json() : null;
    }
}