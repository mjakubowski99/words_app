<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Infrastructure\Mappers\SmTwoFlashcardMapper;
use Flashcard\Infrastructure\Mappers\FlashcardFromSmTwoMapper;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;

class SmTwoFlashcardRepository implements ISmTwoFlashcardRepository
{
    public function __construct(
        private SmTwoFlashcardMapper $mapper,
        private FlashcardFromSmTwoMapper $flashcard_mapper,
    ) {}

    public function findMany(Owner $owner, array $flashcard_ids): SmTwoFlashcards
    {
        return $this->mapper->findMany($owner, $flashcard_ids);
    }

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void
    {
        $this->mapper->saveMany($sm_two_flashcards);
    }

    public function getFlashcardsByLowestRepetitionInterval(Owner $owner, int $limit, array $exclude_flashcard_ids, bool $skip_hard=false): array
    {
        return $this->flashcard_mapper->getFlashcardsWithLowerRepetitionInterval($owner, $limit, $exclude_flashcard_ids, $skip_hard);
    }

    public function getFlashcardsByLowestRepetitionIntervalAndCategory(CategoryId $category_id, int $limit, array $exclude_flashcard_ids, bool $skip_hard=false): array
    {
        return $this->flashcard_mapper->getFlashcardsByLowestRepetitionIntervalByCategory($category_id, $limit, $exclude_flashcard_ids, $skip_hard);
    }
}
