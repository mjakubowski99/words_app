<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\CategoryId;

interface ISmTwoFlashcardRepository
{
    public function findMany(Owner $owner, array $flashcard_ids): SmTwoFlashcards;

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void;

    /** @return Flashcard[] */
    public function getNextFlashcardsByCategory(CategoryId $category_id, int $limit, array $exclude_flashcard_ids, bool $get_oldest = false): array;

    /** @return Flashcard[] */
    public function getNextFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids, bool $get_oldest = false): array;
}
