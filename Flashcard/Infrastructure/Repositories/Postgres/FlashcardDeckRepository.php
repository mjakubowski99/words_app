<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\Deck;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardDeckMapper;

class FlashcardDeckRepository implements IFlashcardDeckRepository
{
    public function __construct(
        private readonly FlashcardDeckMapper $mapper
    ) {}

    public function findById(FlashcardDeckId $id): Deck
    {
        return $this->mapper->findById($id);
    }

    public function searchByName(UserId $user_id, string $name): ?Deck
    {
        return $this->mapper->searchByName($user_id, $name);
    }

    public function searchByNameAdmin(string $name): ?Deck
    {
        return $this->mapper->searchByNameAdmin($name);
    }

    public function create(Deck $deck): Deck
    {
        $deck_id = $this->mapper->create($deck);

        return $this->findById($deck_id);
    }

    public function update(Deck $deck): void
    {
        $this->mapper->update($deck);
    }

    /** @return Deck[] */
    public function getByUser(UserId $user_id, int $page, int $per_page): array
    {
        return $this->mapper->getByUser($user_id, $page, $per_page);
    }

    public function remove(Deck $deck): void
    {
        $this->mapper->remove($deck->getId());
    }

    public function deleteAllForUser(UserId $user_id): void
    {
        $this->mapper->deleteAllForUser($user_id);
    }
}
