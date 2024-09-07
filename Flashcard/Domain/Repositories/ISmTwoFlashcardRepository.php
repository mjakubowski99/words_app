<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;

interface ISmTwoFlashcardRepository
{
    public function create(SmTwoFlashcard $flashcard): void;

    public function findMany(UserId $user_id, array $flashcard_ids): SmTwoFlashcards;

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void;

    /** @return Flashcard[] */
    public function getFlashcardsWithLowestRepetitionIntervalByCategory(UserId $user_id, CategoryId $category_id, int $limit): array;

    public function getFlashcardsWithLowestRepetitionInterval(UserId $user_id, int $limit): array;
}
