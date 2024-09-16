<?php

declare(strict_types=1);

namespace Shared\Integrations\Gemini;

interface IGenerateTextResponse
{
    public function success(): bool;

    public function getStatusCode(): int;

    public function getPromptTokensCount(): int;

    public function getResponseTokenCount(): int;

    public function getGeneratedText(): ?string;

    public function getSuccessResponse(): ?array;

    public function getErrorReason(): ?string;

    public function getErrorResponse(): ?array;
}
