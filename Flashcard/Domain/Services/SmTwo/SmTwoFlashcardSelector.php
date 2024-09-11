<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services\SmTwo;

use Flashcard\Domain\Models\Session;
use Shared\Enum\FlashcardCategoryType;
use Flashcard\Domain\Services\IFlashcardSelector;
use Flashcard\Domain\Repositories\IFlashcardRepository;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;

class SmTwoFlashcardSelector implements IFlashcardSelector
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository,
        private ISessionFlashcardRepository $session_flashcard_repository,
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function select(Session $session, int $limit): array
    {
        return match ($session->getFlashcardCategory()->getCategoryType()) {
            FlashcardCategoryType::GENERAL => $this->selectGeneral($session, $limit),
            FlashcardCategoryType::NORMAL => $this->selectNormal($session, $limit),
            default => throw new \Exception('Not supported type'),
        };
    }

    private function selectGeneral(Session $session, int $limit): array
    {
        $latest_ids = $this->session_flashcard_repository->getLatestSessionFlashcardIds($session->getId(), $limit);

        $flashcards = $this->repository->getFlashcardsWithLowestRepetitionInterval($session->getOwner(), $limit, $latest_ids);

        if (count($flashcards) !== $limit) {
            $random_flashcards = $this->flashcard_repository->getRandomFlashcards($session->getOwner(), $limit, $latest_ids);
            $flashcards = array_merge($random_flashcards, $flashcards);
        }

        return $flashcards;
    }

    private function selectNormal(Session $session, int $limit): array
    {
        $latest_ids = $this->session_flashcard_repository->getLatestSessionFlashcardIds($session->getId(), $limit);
        $category = $session->getFlashcardCategory();

        $flashcards = $this->repository->getFlashcardsWithLowestRepetitionIntervalByCategory($category->getId(), $limit, $latest_ids);

        if (count($flashcards) !== $limit) {
            $limit = $limit - count($flashcards);

            $random_flashcards = $this->flashcard_repository->getRandomFlashcardsByCategory($category->getId(), $limit, $latest_ids);

            $flashcards = array_merge($random_flashcards, $flashcards);
        }

        return $flashcards;
    }
}
