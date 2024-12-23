<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\FlashcardSortCriteria;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;
use Flashcard\Infrastructure\Mappers\Postgres\SmTwoFlashcardMapper;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardFromSmTwoMapper;
use Flashcard\Infrastructure\SortCriteria\Postgres\PostgresSortCriteria;
use Flashcard\Infrastructure\Factories\Postgres\FlashcardSortCriteriaFactory;

class SmTwoFlashcardRepository implements ISmTwoFlashcardRepository
{
    public function __construct(
        private SmTwoFlashcardMapper $mapper,
        private FlashcardFromSmTwoMapper $flashcard_mapper,
        private FlashcardSortCriteriaFactory $sort_criteria_factory,
    ) {}

    public function findMany(UserId $user_id, array $flashcard_ids): SmTwoFlashcards
    {
        return $this->mapper->findMany($user_id, $flashcard_ids);
    }

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void
    {
        $this->mapper->saveMany($sm_two_flashcards);
    }

    public function getNextFlashcardsByUser(UserId $user_id, int $limit, array $exclude_flashcard_ids, array $sort_criteria): array
    {
        return $this->flashcard_mapper->getNextFlashcards($user_id, $limit, $exclude_flashcard_ids, $this->buildCriteria($sort_criteria));
    }

    public function getNextFlashcardsByDeck(FlashcardDeckId $deck_id, int $limit, array $exclude_flashcard_ids, array $sort_criteria): array
    {
        return $this->flashcard_mapper->getNextFlashcardsByDeck($deck_id, $limit, $exclude_flashcard_ids, $this->buildCriteria($sort_criteria));
    }

    /**
     * @param  FlashcardSortCriteria[] $sort_criteria
     * @return PostgresSortCriteria[]
     */
    private function buildCriteria(array $sort_criteria): array
    {
        return array_map(function (FlashcardSortCriteria $criteria) {
            return $this->sort_criteria_factory->make($criteria);
        }, $sort_criteria);
    }
}
