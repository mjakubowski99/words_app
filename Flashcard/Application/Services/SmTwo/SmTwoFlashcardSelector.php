<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\FlashcardSortCriteria;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;

class SmTwoFlashcardSelector implements IFlashcardSelector
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository,
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function resetRepetitionsInSession(UserId $user_id): void
    {
        $this->repository->resetRepetitionsInSession($user_id);
    }

    public function select(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        if ($next_session_flashcards->hasDeck()) {
            return $this->selectNormal($next_session_flashcards, $limit);
        }

        return $this->selectGeneral($next_session_flashcards, $limit);
    }

    private function selectGeneral(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_limit = max(3, (int) ($next_session_flashcards->getMaxFlashcardsCount() * 0.2));
        $latest_limit = min(5, $latest_limit);

        $prioritize_not_hard_flashcards = $next_session_flashcards->getCurrentSessionFlashcardsCount() % 5 === 0;

        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $latest_limit);

        $criteria = $prioritize_not_hard_flashcards ? [
            FlashcardSortCriteria::EVER_NOT_VERY_GOOD_FIRST,
            FlashcardSortCriteria::PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST,
            FlashcardSortCriteria::OLDER_THAN_FIFTEEN_SECONDS_AGO,
            FlashcardSortCriteria::NOT_HARD_FLASHCARDS_FIRST,
            FlashcardSortCriteria::RANDOMIZE_LATEST_FLASHCARDS_ORDER,
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
        ] : [
            FlashcardSortCriteria::EVER_NOT_VERY_GOOD_FIRST,
            FlashcardSortCriteria::PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST,
            FlashcardSortCriteria::OLDER_THAN_FIFTEEN_SECONDS_AGO,
            FlashcardSortCriteria::HARD_FLASHCARDS_FIRST,
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
            FlashcardSortCriteria::HARD_FLASHCARDS_FIRST,
            FlashcardSortCriteria::RANDOMIZE_LATEST_FLASHCARDS_ORDER,
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
        ];

        $results = $this->repository->getNextFlashcardsByUser($next_session_flashcards->getUserId(), $limit, $latest_ids, $criteria, $next_session_flashcards->getMaxFlashcardsCount());

        if (count($results) < $limit) {
            return $this->repository->getNextFlashcardsByUser($next_session_flashcards->getUserId(), $limit, [], $criteria, $next_session_flashcards->getMaxFlashcardsCount());
        }

        return $results;
    }

    private function selectNormal(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_limit = max(3, (int) ($next_session_flashcards->getMaxFlashcardsCount() * 0.2));
        $latest_limit = min(5, $latest_limit);

        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $latest_limit);
        $deck = $next_session_flashcards->getDeck();

        $prioritize_not_hard_flashcards = $next_session_flashcards->getCurrentSessionFlashcardsCount() % 5 === 0;

        $criteria = $prioritize_not_hard_flashcards ? [
            FlashcardSortCriteria::NOT_RATED_FLASHCARDS_FIRST,
            FlashcardSortCriteria::EVER_NOT_VERY_GOOD_FIRST,
            FlashcardSortCriteria::OLDER_THAN_FIFTEEN_SECONDS_AGO,
            FlashcardSortCriteria::PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST,
            FlashcardSortCriteria::NOT_HARD_FLASHCARDS_FIRST,
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
        ] : [
            FlashcardSortCriteria::NOT_RATED_FLASHCARDS_FIRST,
            FlashcardSortCriteria::EVER_NOT_VERY_GOOD_FIRST,
            FlashcardSortCriteria::OLDER_THAN_FIFTEEN_SECONDS_AGO,
            FlashcardSortCriteria::HARD_FLASHCARDS_FIRST,
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
            FlashcardSortCriteria::PLANNED_FLASHCARDS_FOR_CURRENT_DATE_FIRST,
            FlashcardSortCriteria::HARD_FLASHCARDS_FIRST,
            FlashcardSortCriteria::OLDEST_UPDATE_FLASHCARDS_FIRST,
        ];

        $results = $this->repository->getNextFlashcardsByDeck($next_session_flashcards->getUserId(), $deck->getId(), $limit, $latest_ids, $criteria, $next_session_flashcards->getMaxFlashcardsCount());

        if (count($results) < $limit) {
            return $this->repository->getNextFlashcardsByDeck($next_session_flashcards->getUserId(), $deck->getId(), $limit, [], $criteria, $next_session_flashcards->getMaxFlashcardsCount());
        }

        return $results;
    }
}
