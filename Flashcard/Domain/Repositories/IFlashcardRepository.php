<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Owner;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\CategoryId;

interface IFlashcardRepository
{
    /** @param Flashcard[] $flashcards */
    public function createMany(array $flashcards): void;

    public function getRandomFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids): array;

    public function getRandomFlashcardsByCategory(CategoryId $id, int $limit, array $exclude_flashcard_ids): array;

    /** @return Flashcard[] **/
    public function getByCategory(CategoryId $category_id): array;
}
