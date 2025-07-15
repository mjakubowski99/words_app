<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;

interface IFlashcardRepository
{
    public function find(FlashcardId $id): Flashcard;

    public function findMany(array $flashcard_ids): array;

    /** @param Flashcard[] $flashcards */
    public function createMany(array $flashcards): void;

    public function update(Flashcard $flashcard): void;

    public function delete(FlashcardId $id): void;

    /** @param FlashcardId[] $flashcard_ids */
    public function bulkDelete(UserId $user_id, array $flashcard_ids);

    public function getStoryIdForFlashcards(array $flashcard_ids);

    public function deleteAllForUser(UserId $user_id): void;

    /** @return Flashcard[] */
    public function getRandomFlashcards(UserId $user_id, int $limit, array $exclude_flashcard_ids): array;

    /** @return Flashcard[] */
    public function getRandomFlashcardsByCategory(FlashcardDeckId $id, int $limit, array $exclude_flashcard_ids): array;

    /** @return Flashcard[] */
    public function getByDeck(FlashcardDeckId $deck_id): array;

    /** @return FlashcardId[] */
    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array;

    public function replaceDeck(FlashcardDeckId $actual_deck, FlashcardDeckId $new_deck): bool;

    public function replaceInSessions(FlashcardDeckId $actual_deck, FlashcardDeckId $new_deck): bool;

    public function hasAnySessions(FlashcardId $id): bool;
}
