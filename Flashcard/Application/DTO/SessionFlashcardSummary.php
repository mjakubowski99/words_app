<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Shared\Utils\ValueObjects\Language;
use Shared\Flashcard\ISessionFlashcardSummary;

class SessionFlashcardSummary implements ISessionFlashcardSummary
{
    private int $flashcard_id;
    private string $frontWord;
    private string $backWord;
    private string $frontContext;
    private string $backContext;
    private Language $frontLang;
    private Language $backLang;
    private string $emoji;

    public function __construct(
        int $flashcard_id,
        string $frontWord,
        string $backWord,
        string $frontContext,
        string $backContext,
        Language $frontLang,
        Language $backLang,
        string $emoji
    ) {
        $this->flashcard_id = $flashcard_id;
        $this->frontWord = $frontWord;
        $this->backWord = $backWord;
        $this->frontContext = $frontContext;
        $this->backContext = $backContext;
        $this->frontLang = $frontLang;
        $this->backLang = $backLang;
        $this->emoji = $emoji;
    }

    public function getFlashcardId(): int
    {
        return $this->flashcard_id;
    }

    public function getFrontWord(): string
    {
        return $this->frontWord;
    }

    public function getBackWord(): string
    {
        return $this->backWord;
    }

    public function getFrontContext(): string
    {
        return $this->frontContext;
    }

    public function getBackContext(): string
    {
        return $this->backContext;
    }

    public function getFrontLang(): Language
    {
        return $this->frontLang;
    }

    public function getBackLang(): Language
    {
        return $this->backLang;
    }

    public function getEmoji(): string
    {
        return $this->emoji;
    }
}
