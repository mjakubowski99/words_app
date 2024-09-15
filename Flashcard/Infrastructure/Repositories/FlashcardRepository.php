<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Infrastructure\Mappers\FlashcardMapper;

class FlashcardRepository implements IFlashcardRepository
{
    public function __construct(private FlashcardMapper $mapper) {}

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
}
