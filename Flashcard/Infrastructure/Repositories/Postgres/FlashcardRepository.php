<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Infrastructure\Mappers\Postgres\FlashcardMapper;
use Flashcard\Infrastructure\Mappers\Postgres\SessionFlashcardMapper;

class FlashcardRepository implements IFlashcardRepository
{
    public function __construct(
        private FlashcardMapper $mapper,
        private SessionFlashcardMapper $session_flashcard_mapper,
    ) {}

    /** @return Flashcard[] */
    public function getRandomFlashcards(Owner $owner, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->mapper->getRandomFlashcards($owner, $limit, $exclude_flashcard_ids);
    }

    public function getRandomFlashcardsByCategory(FlashcardDeckId $id, int $limit, array $exclude_flashcard_ids): array
    {
        return $this->mapper->getRandomFlashcardsByCategory($id, $limit, $exclude_flashcard_ids);
    }

    public function createMany(array $flashcards): void
    {
        $this->mapper->createMany($flashcards);
    }

    public function find(FlashcardId $id): Flashcard
    {
        return $this->mapper->find($id);
    }

    public function update(Flashcard $flashcard): void
    {
        $this->mapper->update($flashcard);
    }

    public function delete(FlashcardId $id): void
    {
        $this->mapper->delete($id);
    }

    public function getByDeck(FlashcardDeckId $deck_id): array
    {
        return $this->mapper->getByCategory($deck_id);
    }

    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array
    {
        return $this->session_flashcard_mapper->getLatestSessionFlashcardIds($session_id, $limit);
    }

    public function replaceDeck(FlashcardDeckId $actual_deck, FlashcardDeckId $new_deck): bool
    {
        return $this->mapper->replaceDeck($actual_deck, $new_deck);
    }

    public function replaceInSessions(FlashcardDeckId $actual_deck, FlashcardDeckId $new_deck): bool
    {
        return $this->mapper->replaceInSessions($actual_deck, $new_deck);
    }
}
