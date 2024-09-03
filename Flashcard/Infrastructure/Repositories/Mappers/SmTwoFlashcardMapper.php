<?php

namespace Flashcard\Infrastructure\Repositories\Mappers;

use Flashcard\Domain\Models\SmTwoFlashcard;
use Shared\Utils\ValueObjects\UserId;

class SmTwoFlashcardMapper
{
    public function __construct(private FlashcardMapper $flashcard_mapper) {}


    public function map(array $data): SmTwoFlashcard
    {
        return new SmTwoFlashcard(
            new UserId($data['sm_two_flashcards_user_id']),
            $this->flashcard_mapper->map($data),
            $data['sm_two_flashcards_repetition_ratio'],
            $data['sm_two_flashcards_repetition_interval'],
            $data['sm_two_flashcards_repetition_count'],
        );
    }
}