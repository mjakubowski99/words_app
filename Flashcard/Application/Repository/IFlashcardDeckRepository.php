<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Flashcard\Domain\Models\Deck;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

interface IFlashcardDeckRepository
{
    public function findById(FlashcardDeckId $id): Deck;

    public function searchByName(UserId $user_id, string $name): ?Deck;

    /** @return Deck[] */
    public function getByUser(UserId $user_id, int $page, int $per_page): array;

    public function create(Deck $deck): Deck;

    public function update(Deck $deck): void;

    public function remove(Deck $deck): void;
}
