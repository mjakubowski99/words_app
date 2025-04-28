<?php

namespace Flashcard\Application\DTO;

use Shared\Enum\Language;
use Shared\Flashcard\ISessionFlashcardSummary;

class SessionFlashcardSummary implements ISessionFlashcardSummary
{
    private int $sessionFlashcardId;
    private string $frontWord;
    private string $backWord;
    private string $frontContext;
    private string $backContext;
    private Language $frontLang;
    private Language $backLang;
    private string $emoji;

    public function __construct(
        int $sessionFlashcardId,
        string $frontWord,
        string $backWord,
        string $frontContext,
        string $backContext,
        Language $frontLang,
        Language $backLang,
        string $emoji
    ) {
        $this->sessionFlashcardId = $sessionFlashcardId;
        $this->frontWord = $frontWord;
        $this->backWord = $backWord;
        $this->frontContext = $frontContext;
        $this->backContext = $backContext;
        $this->frontLang = $frontLang;
        $this->backLang = $backLang;
        $this->emoji = $emoji;
    }

    public function getSessionFlashcardId(): int
    {
        return $this->sessionFlashcardId;
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