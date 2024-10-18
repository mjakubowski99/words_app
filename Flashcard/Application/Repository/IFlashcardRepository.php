<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\FlashcardId;

interface IFlashcardRepository
{
    /** @param Flashcard[] $flashcards */
    public function createMany(array $flashcards): void;

    public function getRandomFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids): array;

    public function getRandomFlashcardsByCategory(CategoryId $id, int $limit, array $exclude_flashcard_ids): array;

    /** @return Flashcard[] */
    public function getByCategory(CategoryId $category_id): array;

    /** @return FlashcardId[] */
    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array;

    public function replaceCategory(CategoryId $actual_category, CategoryId $new_category): bool;
}
