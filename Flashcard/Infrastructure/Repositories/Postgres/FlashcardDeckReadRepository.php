<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\ReadModels\DeckDetailsRead;
use Flashcard\Application\ReadModels\OwnerCategoryRead;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardReadMapper;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardDeckReadMapper;

class FlashcardDeckReadRepository implements IFlashcardDeckReadRepository
{
    public function __construct(
        private readonly FlashcardDeckReadMapper $mapper,
        private readonly FlashcardReadMapper $flashcard_mapper,
    ) {}

    public function findRatingStats(FlashcardDeckId $id): RatingStatsReadCollection
    {
        return $this->flashcard_mapper->findFlashcardStats($id, null);
    }

    public function findDetails(UserId $user_id, FlashcardDeckId $id, ?string $search, int $page, int $per_page): DeckDetailsRead
    {
        return $this->mapper->findDetails($user_id, $id, $search, $page, $per_page);
    }

    /** @return OwnerCategoryRead[] */
    public function getByUser(UserId $user_id, ?string $search, int $page, int $per_page): array
    {
        return $this->mapper->getByUser($user_id, $search, $page, $per_page);
    }

    /** @return OwnerCategoryRead[] */
    public function getAdminDecks(UserId $user_id, ?string $search, int $page, int $per_page): array
    {
        return $this->mapper->getAdminDecks($user_id, $search, $page, $per_page);
    }
}
