<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Shared\Enum\FlashcardCategoryType;
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
        return match ($next_session_flashcards->getCategory()->getCategoryType()) {
            FlashcardCategoryType::GENERAL => $this->selectGeneral($next_session_flashcards, $limit),
            FlashcardCategoryType::NORMAL => $this->selectNormal($next_session_flashcards, $limit),
        };
    }

    private function selectGeneral(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_limit = 2;

        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $latest_limit);

        $results = $this->repository->getFlashcardsByRepetitionIntervalProbability($next_session_flashcards->getOwner(), $limit, $latest_ids);

        if (count($results) < $limit) {
            return $this->repository->getFlashcardsByRepetitionIntervalProbability($next_session_flashcards->getOwner(), $limit, []);
        }

        return $results;
    }

    private function selectNormal(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_limit = 2;
        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $latest_limit);
        $category = $next_session_flashcards->getCategory();

        $results = $this->repository->getFlashcardsByRepetitionIntervalProbabilityAndCategory($category->getId(), $limit, $latest_ids);

        if (count($results) < $limit) {
            return $this->repository->getFlashcardsByRepetitionIntervalProbabilityAndCategory($category->getId(), $limit, []);
        }

        return $results;
    }
}
