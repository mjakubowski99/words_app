<?php

declare(strict_types=1);

namespace Flashcard\Application\Services\SmTwo;

use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Application\Repository\ISessionFlashcardRepository;
use Shared\Enum\FlashcardCategoryType;

class SmTwoFlashcardSelector implements IFlashcardSelector
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository,
        private ISessionFlashcardRepository $session_flashcard_repository,
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function select(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        return match ($next_session_flashcards->getCategory()->getCategoryType()) {
            FlashcardCategoryType::GENERAL => $this->selectGeneral($next_session_flashcards, $limit),
            FlashcardCategoryType::NORMAL => $this->selectNormal($next_session_flashcards, $limit),
            default => throw new \Exception('Not supported type'),
        };
    }

    private function selectGeneral(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_ids = $this->session_flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $limit);

        $flashcards = $this->repository->getFlashcardsWithLowestRepetitionInterval($next_session_flashcards->getOwner(), $limit, $latest_ids);

        if (count($flashcards) !== $limit) {
            $random_flashcards = $this->flashcard_repository->getRandomFlashcards($next_session_flashcards->getOwner(), $limit, $latest_ids);
            $flashcards = array_merge($random_flashcards, $flashcards);
        }

        return $flashcards;
    }

    private function selectNormal(NextSessionFlashcards $next_session_flashcards, int $limit): array
    {
        $latest_ids = $this->session_flashcard_repository->getLatestSessionFlashcardIds($next_session_flashcards->getSessionId(), $limit);
        $category = $next_session_flashcards->getCategory();

        $flashcards = $this->repository->getFlashcardsWithLowestRepetitionIntervalByCategory($category->getId(), $limit, $latest_ids);

        if (count($flashcards) !== $limit) {
            $limit = $limit - count($flashcards);

            $random_flashcards = $this->flashcard_repository->getRandomFlashcardsByCategory($category->getId(), $limit, $latest_ids);

            $flashcards = array_merge($random_flashcards, $flashcards);
        }

        return $flashcards;
    }
}
