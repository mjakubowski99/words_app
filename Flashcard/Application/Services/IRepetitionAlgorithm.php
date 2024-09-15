<?php

declare(strict_types=1);

namespace Flashcard\Application\Services;
use Flashcard\Domain\Models\RateableSessionFlashcards;

interface IRepetitionAlgorithm
{
    public function handle(RateableSessionFlashcards $session_flashcards): void;
}
