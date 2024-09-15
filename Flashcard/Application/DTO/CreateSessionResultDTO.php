<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\ValueObjects\SessionId;

final readonly class CreateSessionResultDTO
{
    public function __construct(
        private bool $success,
        private ?string $fail_reason,
        private ?SessionId $session_id,
    ) {}

    public function success(): bool
    {
        return $this->success;
    }

    public function getFailReason(): string
    {
        return $this->fail_reason;
    }

    public function getId(): SessionId
    {
        return $this->session_id;
    }
}
