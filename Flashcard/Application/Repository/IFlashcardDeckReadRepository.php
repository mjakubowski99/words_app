<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

interface IFlashcardDeckReadRepository
{
    public function findRatingStats(FlashcardDeckId $id): RatingStatsReadCollection;

    public function findDetails(FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead;

    /** @return OwnerCategoryRead[] */
    public function getByOwner(Owner $owner, ?string $search, int $page, int $per_page): array;
}
