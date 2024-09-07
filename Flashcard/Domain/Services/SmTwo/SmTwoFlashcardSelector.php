<?php

declare(strict_types=1);

namespace Flashcard\Domain\Services\SmTwo;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\FlashcardCategory;
use Flashcard\Domain\Services\IFlashcardSelector;
use Flashcard\Domain\Repositories\IFlashcardRepository;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;

class SmTwoFlashcardSelector implements IFlashcardSelector
{
    public function __construct(
        private ISmTwoFlashcardRepository $repository,
        private IFlashcardRepository $flashcard_repository,
    ) {}

    public function select(UserId $user_id, FlashcardCategory $category, int $limit): array
    {
        $flashcards = $category->isMainCategory() ?
            $this->repository->getFlashcardsWithLowestRepetitionInterval($user_id, $limit)
            : $this->repository->getFlashcardsWithLowestRepetitionIntervalByCategory($user_id, $category->getId(), $limit);

        if (count($flashcards) !== $limit) {
            $limit = $limit - count($flashcards);

            $random_flashcards = $category->isMainCategory() ?
                $this->flashcard_repository->getRandomFlashcards($user_id, $limit) :
                $this->flashcard_repository->getRandomFlashcardsByCategory($category->getId(), $limit);

            $flashcards = array_merge($random_flashcards, $flashcards);
        }

        return $flashcards;
    }
}
