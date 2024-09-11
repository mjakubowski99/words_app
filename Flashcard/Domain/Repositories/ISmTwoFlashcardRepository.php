<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;

interface ISmTwoFlashcardRepository
{
    public function create(SmTwoFlashcard $flashcard): void;

    public function findMany(Owner $owner, array $flashcard_ids): SmTwoFlashcards;

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void;

    /** @return Flashcard[] */
    public function getFlashcardsWithLowestRepetitionIntervalByCategory(CategoryId $category_id, int $limit, array $exclude_flashcard_ids): array;

    public function getFlashcardsWithLowestRepetitionInterval(Owner $owner, int $limit, array $exclude_flashcard_ids): array;
}
