<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\Repository\IFlashcardReadRepository;
use Flashcard\Application\ReadModels\RatingStatsReadCollection;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardReadMapper;

class FlashcardReadRepository implements IFlashcardReadRepository
{
    public function __construct(private FlashcardReadMapper $mapper) {}

    public function findStatsByUser(UserId $user_id): RatingStatsReadCollection
    {
        return $this->mapper->findFlashcardStats(null, $user_id);
    }

    public function findByUser(UserId $user_id, ?string $search, int $page, int $per_page): UserFlashcardsRead
    {
        return $this->mapper->getByUser($user_id, $search, $page, $per_page);
    }
}
