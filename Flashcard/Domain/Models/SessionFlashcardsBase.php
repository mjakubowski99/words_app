<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

abstract class SessionFlashcardsBase
{
    public function hasFlashcardPoll(): bool
    {
        return !$this->hasDeck();
    }

    abstract public function hasDeck(): bool;
}
