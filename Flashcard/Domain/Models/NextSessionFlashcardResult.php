<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

class NextSessionFlashcardResult
{
    public function __construct(
        private SessionFlashcardId $session_flashcard_id,
        private FlashcardId $flashcard_id,
    ) {}

    public function getId(): SessionFlashcardId
    {
        return $this->session_flashcard_id;
    }

    public function getFlashcardId(): FlashcardId
    {
        return $this->flashcard_id;
    }
}
