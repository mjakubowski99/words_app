<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\Flashcard;
use Shared\Models\Emoji;
use Shared\Utils\ValueObjects\Language;
use Shared\Flashcard\ISessionFlashcardSummary;

class SessionFlashcardSummary implements ISessionFlashcardSummary
{
    public function __construct(
        private Flashcard $flashcard,
        private bool $is_additional,
        private bool $is_story_part,
        private ?string $story_sentence
    ) {
    }

    public function getFlashcard(): Flashcard
    {
        return $this->flashcard;
    }

    public function getFlashcardId(): int
    {
        return $this->flashcard->getId()->getValue();
    }

    public function getFrontWord(): string
    {
        return $this->flashcard->getFrontWord();
    }

    public function getBackWord(): string
    {
        return $this->flashcard->getBackWord();
    }

    public function getFrontContext(): string
    {
        return $this->flashcard->getFrontContext();
    }

    public function getBackContext(): string
    {
        return $this->flashcard->getBackContext();
    }

    public function getFrontLang(): Language
    {
        return $this->flashcard->getFrontLang();
    }

    public function getBackLang(): Language
    {
        return $this->flashcard->getBackLang();
    }

    public function getEmoji(): ?Emoji
    {
        return $this->flashcard->getEmoji();
    }

    public function getIsAdditional(): bool
    {
        return $this->is_additional;
    }

    public function getIsStoryPart(): bool
    {
        return $this->is_story_part;
    }

    public function getStorySentence(): ?string
    {
        return $this->story_sentence;
    }
}
