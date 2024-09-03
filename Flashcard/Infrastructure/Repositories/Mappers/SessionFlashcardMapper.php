<?php

namespace Flashcard\Infrastructure\Repositories\Mappers;

use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcardId;

class SessionFlashcardMapper
{
    public function map(array $data): SessionFlashcard
    {
        return new SessionFlashcard(
            new SessionFlashcardId($data['learning_session_flashcards_id']),
            new FlashcardId($data['learning_session_flashcards_flashcard_id']),
            $data['learning_session_flashcards_rating'] !== null ? Rating::from($data['learning_session_flashcards_rating']) : null,
        );
    }
}