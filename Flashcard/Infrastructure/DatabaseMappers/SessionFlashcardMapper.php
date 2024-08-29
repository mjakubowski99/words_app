<?php

namespace Flashcard\Infrastructure\DatabaseMappers;

use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcardId;

class SessionFlashcardMapper extends AbstractMapper
{
    public const COLUMNS = ['id', 'rating'];

    public function __construct(
        private readonly FlashcardMapper $flashcard_mapper,
    ) {}

    public function map(array $data): SessionFlashcard
    {
        $flashcard = $this->flashcard_mapper->map($data);

        return new SessionFlashcard(
            new SessionFlashcardId($data['learning_session_flashcards_id']),
            $flashcard,
            Rating::from($data['learning_session_flashcards_rating'])
        );
    }
}