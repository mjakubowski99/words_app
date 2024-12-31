<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;

interface IFlashcardDeckReadRepository
{
    public function findRatingStats(FlashcardDeckId $id): RatingStatsReadCollection;

    public function findDetails(UserId $user_id, FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead;

    public function getByUser(UserId $user_id, ?string $search, int $page, int $per_page): array;
}
