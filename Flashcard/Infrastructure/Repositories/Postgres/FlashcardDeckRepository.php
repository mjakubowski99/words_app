<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardDeckMapper;
use Shared\Utils\ValueObjects\UserId;

class FlashcardDeckRepository implements IFlashcardDeckRepository
{
    public function __construct(
        private readonly FlashcardDeckMapper $mapper
    ) {}

    public function findById(FlashcardDeckId $id): Deck
    {
        return $this->mapper->findById($id);
    }

    public function searchByName(Owner $owner, string $name): ?Deck
    {
        return $this->mapper->searchByName($owner, $name);
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
    public function getByOwner(Owner $owner, int $page, int $per_page): array
    {
        return $this->mapper->getByOwner($owner, $page, $per_page);
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
