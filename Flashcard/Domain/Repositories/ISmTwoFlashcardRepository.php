<?php

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Shared\Utils\ValueObjects\UserId;

interface ISmTwoFlashcardRepository
{
    public function create(SmTwoFlashcard $flashcard): void;

    /** @return SmTwoFlashcard[] */
    public function findMany(UserId $user_id, array $flashcard_ids): array;

    /** @param  SmTwoFlashcard[] $sm_two_flashcards */
    public function saveMany(array $sm_two_flashcards): void;

    /** @return Flashcard[] */
    public function getFlashcardsWithLowestRepetitionInterval(UserId $user_id, CategoryId $category_id, int $limit): array;
}