<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Infrastructure\Mappers\FlashcardMapper;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Infrastructure\Mappers\SessionFlashcardMapper;

class FlashcardRepository implements IFlashcardRepository
{
    public function __construct(
        private FlashcardMapper $mapper,
        private SessionFlashcardMapper $session_flashcard_mapper,
    ) {}

    public function getRandomFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->mapper->getRandomFlashcards($owner, $limit, $exclude_flashcard_ids);
    }

    public function getRandomFlashcardsByCategory(CategoryId $id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->mapper->getRandomFlashcardsByCategory($id, $limit, $exclude_flashcard_ids);
    }

    public function createMany(array $flashcards): void
    {
        $this->mapper->createMany($flashcards);
    }

    public function getByCategory(CategoryId $category_id): array
    {
        return $this->mapper->getByCategory($category_id);
    }

    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array
    {
        return $this->session_flashcard_mapper->getLatestSessionFlashcardIds($session_id, $limit);
    }

    public function replaceCategory(CategoryId $actual_category, CategoryId $new_category): bool
    {
        return $this->mapper->replaceCategory($actual_category, $new_category);
    }

    public function replaceInSessions(CategoryId $actual_category, CategoryId $new_category): bool
    {
        return $this->mapper->replaceInSessions($actual_category, $new_category);
    }
}
