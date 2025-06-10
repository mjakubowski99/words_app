<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Shared\Models\Emoji;
use Shared\Utils\ValueObjects\Language;
use Shared\Flashcard\ISessionFlashcardSummary;

class SessionFlashcardSummary implements ISessionFlashcardSummary
{
    private int $flashcard_id;
    private string $front_word;
    private string $back_word;
    private string $front_context;
    private string $back_context;
    private Language $front_lang;
    private Language $back_lang;
    private ?Emoji $emoji;

    public function __construct(
        int $flashcard_id,
        string $front_word,
        string $back_word,
        string $front_context,
        string $back_context,
        Language $frontLang,
        Language $backLang,
        ?Emoji $emoji
    ) {
        $this->flashcard_id = $flashcard_id;
        $this->front_word = $front_word;
        $this->back_word = $back_word;
        $this->front_context = $front_context;
        $this->back_context = $back_context;
        $this->front_lang = $frontLang;
        $this->back_lang = $backLang;
        $this->emoji = $emoji;
    }

    public function getFlashcardId(): int
    {
        return $this->flashcard_id;
    }

    public function getFrontWord(): string
    {
        return $this->front_word;
    }

    public function getBackWord(): string
    {
        return $this->back_word;
    }

    public function getFrontContext(): string
    {
        return $this->front_context;
    }

    public function getBackContext(): string
    {
        return $this->back_context;
    }

    public function getFrontLang(): Language
    {
        return $this->front_lang;
    }

    public function getBackLang(): Language
    {
        return $this->back_lang;
    }

    public function getEmoji(): ?Emoji
    {
        return $this->emoji;
    }
}
