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
        if ($next_session_flashcards->hasCategory()) {
            return $this->selectNormal($next_session_flashcards, $limit);
        }

        return $this->selectGeneral($next_session_flashcards, $limit);
    }

    private function selectGeneral(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_limit = 2;

        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $latest_limit);

        $results = $this->repository->getFlashcardsByLowestRepetitionInterval($next_session_flashcards->getOwner(), $limit, $latest_ids);

        if (count($results) < $limit) {
            return $this->repository->getFlashcardsByLowestRepetitionInterval($next_session_flashcards->getOwner(), $limit, []);
        }

        return $results;
    }

    private function selectNormal(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_limit = 2;
        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $latest_limit);
        $category = $next_session_flashcards->getCategory();

        $results = $this->repository->getFlashcardsByLowestRepetitionIntervalAndCategory($category->getId(), $limit, $latest_ids);

        if (count($results) < $limit) {
            return $this->repository->getFlashcardsByLowestRepetitionIntervalAndCategory($category->getId(), $limit, []);
        }

        return $results;
    }
}
