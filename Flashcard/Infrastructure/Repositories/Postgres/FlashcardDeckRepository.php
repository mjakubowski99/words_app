<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Infrastructure\Http\FlashcardDeckMapper;
use Shared\Enum\Language;
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

    public function searchByName(UserId $user_id, string $name, Language $front_lang, Language $back_lang): ?Deck
    {
        return $this->mapper->searchByName($user_id, $name, $front_lang, $back_lang);
    }

    public function searchByNameAdmin(string $name, Language $front_lang, Language $back_lang): ?Deck
    {
        return $this->mapper->searchByNameAdmin($name, $front_lang, $back_lang);
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

    public function updateLastViewedAt(FlashcardDeckId $id, UserId $user_id): void
    {
        $this->mapper->updateLastViewedAt($id, $user_id);
    }

    /** @return Deck[] */
    public function getByUser(UserId $user_id, Language $front_lang, Language $back_lang, int $page, int $per_page): array
    {
        return $this->mapper->getByUser($user_id, $front_lang, $back_lang, $page, $per_page);
    }

    public function remove(Deck $deck): void
    {
        $this->mapper->remove($deck->getId());
    }

    public function deleteAllForUser(UserId $user_id): void
    {
        $this->mapper->deleteAllForUser($user_id);
    }

    /** @param FlashcardDeckId[] $flashcard_deck_ids */
    public function bulkDelete(UserId $user_id, array $flashcard_deck_ids): void
    {
        $this->mapper->bulkDelete($user_id, $flashcard_deck_ids);
    }
}
