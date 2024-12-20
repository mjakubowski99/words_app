<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\Owner;
use Flashcard\Application\ReadModels\UserFlashcardsRead;
use Flashcard\Application\Repository\IFlashcardReadRepository;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardReadMapper;

class FlashcardReadRepository implements IFlashcardReadRepository
{
    public function __construct(private FlashcardReadMapper $mapper) {}

    public function findByUser(Owner $owner, ?string $search, int $page, int $per_page): UserFlashcardsRead
    {
        return $this->mapper->getByUser($owner, $search, $page, $per_page);
    }
}
