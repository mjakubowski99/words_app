<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;

class SmTwoFlashcardSelector implements IFlashcardSelector
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository,
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function select(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        if ($next_session_flashcards->hasDeck()) {
            return $this->selectNormal($next_session_flashcards, $limit);
        }

        return $this->selectGeneral($next_session_flashcards, $limit);
    }

    private function selectGeneral(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_limit = 2;

        $get_latest = $next_session_flashcards->getCurrentSessionFlashcardsCount() % 5 === 0;

        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $latest_limit);

        $results = $this->repository->getNextFlashcards($next_session_flashcards->getOwner(), $limit, $latest_ids, $get_latest);

        if (count($results) < $limit) {
            return $this->repository->getNextFlashcards($next_session_flashcards->getOwner(), $limit, []);
        }

        return $results;
    }

    private function selectNormal(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_limit = 2;
        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $latest_limit);
        $deck = $next_session_flashcards->getDeck();

        $skip_hard = $next_session_flashcards->getCurrentSessionFlashcardsCount() % 5 === 0;

        $results = $this->repository->getNextFlashcardsByDeck($deck->getId(), $limit, $latest_ids, $skip_hard);

        if (count($results) < $limit) {
            return $this->repository->getNextFlashcardsByDeck($deck->getId(), $limit, []);
        }

        return $results;
    }
}
