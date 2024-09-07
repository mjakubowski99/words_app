<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;
use Flashcard\Domain\Models\SmTwoFlashcard;
use Flashcard\Domain\Models\SmTwoFlashcards;
use Flashcard\Infrastructure\Mappers\SmTwoFlashcardMapper;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Infrastructure\Mappers\FlashcardFromSmTwoMapper;

class SmTwoFlashcardRepository implements ISmTwoFlashcardRepository
{
    public function __construct(
        private SmTwoFlashcardMapper $mapper,
        private FlashcardFromSmTwoMapper $flashcard_mapper,
    ) {}

    public function create(SmTwoFlashcard $flashcard): void
    {
        $this->mapper->create($flashcard);
    }

    public function findMany(UserId $user_id, array $flashcard_ids): SmTwoFlashcards
    {
        return $this->mapper->findMany($user_id, $flashcard_ids);
    }

    public function saveMany(SmTwoFlashcards $sm_two_flashcards): void
    {
        $this->mapper->saveMany($sm_two_flashcards);
    }

    public function getFlashcardsWithLowestRepetitionInterval(UserId $user_id, int $limit): array
    {
        return $this->flashcard_mapper->getFlashcardsWithLowestRepetitionInterval($user_id, $limit);
    }

    public function getFlashcardsWithLowestRepetitionIntervalByCategory(UserId $user_id, CategoryId $category_id, int $limit): array
    {
        return $this->flashcard_mapper->getFlashcardsWithLowestRepetitionIntervalByCategory($user_id, $category_id, $limit);
    }
}
