<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Shared\Enum\Language;
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

    public function resetRepetitionsInSession(UserId $user_id): void
    {
        $this->mapper->resetRepetitionsInSession($user_id);
    }

    public function findMany(UserId $user_id, array $flashcard_ids): SmTwoFlashcards
    {
        return $this->mapper->findMany($user_id, $flashcard_ids);
    }

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void
    {
        $this->mapper->saveMany($sm_two_flashcards);
    }

    public function getNextFlashcardsByUser(UserId $user_id, int $limit, array $exclude_flashcard_ids, array $sort_criteria, int $cards_per_session, bool $from_poll, bool $exclude_from_poll, Language $front, Language $back): array
    {
        return $this->flashcard_mapper->getNextFlashcards($user_id, $limit, $exclude_flashcard_ids, $this->buildCriteria($sort_criteria), $cards_per_session, $from_poll, $exclude_from_poll, $front, $back);
    }

    public function getNextFlashcardsByDeck(UserId $user_id, FlashcardDeckId $deck_id, int $limit, array $exclude_flashcard_ids, array $sort_criteria, int $cards_per_session, bool $from_poll, Language $front, Language $back): array
    {
        return $this->flashcard_mapper->getNextFlashcardsByDeck($user_id, $deck_id, $limit, $exclude_flashcard_ids, $this->buildCriteria($sort_criteria), $cards_per_session, $from_poll, $front, $back);
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
