<?php

namespace Flashcard\Domain\Services;

use Flashcard\Domain\Models\SessionFlashcard;

interface IRepetitionAlgorithm
{
    /** @return SessionFlashcard[] */
    public function handle(array $session_flashcards): void;
}