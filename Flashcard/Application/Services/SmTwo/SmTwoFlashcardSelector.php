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
        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), 1);

        return $this->repository->getFlashcardsWithLowestRepetitionInterval($next_session_flashcards->getOwner(), 1, $latest_ids);
    }

    private function selectNormal(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_ids = $this->flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), 1);
        $category = $next_session_flashcards->getCategory();

        return $this->repository->getFlashcardsWithLowestRepetitionIntervalByCategory($category->getId(), 1, $latest_ids);
    }
}
