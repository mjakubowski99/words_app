<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Infrastructure\Mappers\SmTwoFlashcardMapper;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Infrastructure\Mappers\FlashcardFromSmTwoMapper;

class SmTwoFlashcardRepository implements ISmTwoFlashcardRepository
{
    public function __construct(
        private SmTwoFlashcardMapper $mapper,
        private FlashcardFromSmTwoMapper $flashcard_mapper,
    ) {}

    public function create(SmTwoFlashcard $flashcard): void
    {
        $this->mapper->create($flashcard);
    }

    public function findMany(Owner $owner, array $flashcard_ids): SmTwoFlashcards
    {
        return $this->mapper->findMany($owner, $flashcard_ids);
    }

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void
    {
        $this->mapper->saveMany($sm_two_flashcards);
    }

    public function getFlashcardsWithLowestRepetitionInterval(Owner $owner, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->flashcard_mapper->getFlashcardsWithLowestRepetitionInterval($owner, $limit, $exclude_flashcard_ids);
    }

    public function getFlashcardsWithLowestRepetitionIntervalByCategory(CategoryId $category_id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->flashcard_mapper->getFlashcardsWithLowestRepetitionIntervalByCategory($category_id, $limit, $exclude_flashcard_ids);
    }
}
