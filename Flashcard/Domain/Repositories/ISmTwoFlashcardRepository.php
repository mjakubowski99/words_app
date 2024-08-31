<?php

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Shared\Utils\ValueObjects\UserId;

interface ISmTwoFlashcardRepository
{
    public function create(SmTwoFlashcard $flashcard): void;

    public function findMany(UserId $user_id, array $flashcard_ids): SmTwoFlashcards;

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void;

    /** @return Flashcard[] */
    public function getFlashcardsWithLowestRepetitionInterval(UserId $user_id, CategoryId $category_id, int $limit): array;
}