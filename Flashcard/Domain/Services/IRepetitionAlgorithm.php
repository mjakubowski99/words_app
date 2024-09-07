<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\SessionFlashcards;

interface IRepetitionAlgorithm
{
    public function handle(SessionFlashcards $session_flashcards): void;
}
