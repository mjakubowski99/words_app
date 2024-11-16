<?php

declare(strict_types=1);

namespace Tests\Integration\App\Console\Commands;

use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Illuminate\Support\Collection;

trait MergeFlashcardsWithSameCategoryTrait
{
    private function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }

    private function createFlashcardDeck(array $attributes = []): FlashcardDeck
    {
        return FlashcardDeck::factory()->create($attributes);
    }

    private function generateFlashcardsForDecks(array|Collection $decks): array|Collection
    {
        $flashcards = [];
        foreach ($decks as $deck) {
            $flashcards[] = Flashcard::factory()->create(['flashcard_deck_id' => $deck->id]);
        }

        return $flashcards;
    }

    private function assertFlashcardsHasNewDeck(array|Collection $flashcards, FlashcardDeck $expected_deck): void
    {
        foreach ($flashcards as $flashcard) {
            $this->assertDatabaseHas('flashcards', [
                'id' => $flashcard->id,
                'flashcard_deck_id' => $expected_deck->id,
            ]);
        }
    }

    private function assertFlashcardsHasSameDeck(array|Collection $flashcards): void
    {
        foreach ($flashcards as $flashcard) {
            $this->assertDatabaseHas('flashcards', [
                'id' => $flashcard->id,
                'flashcard_deck_id' => $flashcard->flashcard_deck_id,
            ]);
        }
    }

    private function assertFlashcardsDecksDeleted(array|Collection $decks): void
    {
        foreach ($decks as $deck) {
            $this->assertDatabaseMissing('flashcard_decks', [
                'id' => $deck->id,
            ]);
        }
    }
}
