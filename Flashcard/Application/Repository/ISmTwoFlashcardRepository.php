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
    public function getFlashcardsByRepetitionIntervalProbabilityAndCategory(CategoryId $category_id, int $limit, array $exclude_flashcard_ids): array;

    public function getFlashcardsByRepetitionIntervalProbability(Owner $owner, int $limit, array $exclude_flashcard_ids): array;
}
