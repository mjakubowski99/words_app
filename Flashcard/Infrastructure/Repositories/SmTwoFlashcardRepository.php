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

    public function getFlashcardsByRepetitionIntervalProbability(Owner $owner, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->flashcard_mapper->getFlashcardsByRepetitionIntervalProbability($owner, $limit, $exclude_flashcard_ids);
    }

    public function getFlashcardsByRepetitionIntervalProbabilityAndCategory(CategoryId $category_id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->flashcard_mapper->getFlashcardsByRepetitionIntervalProbabilityAndCategory($category_id, $limit, $exclude_flashcard_ids);
    }
}
