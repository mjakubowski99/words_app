<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Console\Commands\MergeFlashcardsWithSameDeckCommand;
use Tests\Integration\App\Console\Commands\MergeFlashcardsWithSameCategoryTrait;

uses(DatabaseTransactions::class);

uses(MergeFlashcardsWithSameCategoryTrait::class);

test('handle merge flashcards to correct user deck', function () {
    // GIVEN
    $user = $this->createUser();
    $decks_to_merge = collect([
        $this->createFlashcardDeck(['name' => 'pies kot', 'user_id' => $user->id]),
        $this->createFlashcardDeck(['name' => 'pies kot', 'user_id' => $user->id]),
    ]);
    $decks_not_to_merge = [
        $this->createFlashcardDeck(['name' => 'jabłko jajko kot', 'user_id' => $user->id]),
        $this->createFlashcardDeck(['name' => 'jabłko jajko kura', 'user_id' => $user->id]),
    ];
    $decks_to_merge = $decks_to_merge->merge([
        $this->createFlashcardDeck(['name' => 'Pies Kot', 'user_id' => $user->id]),
        $this->createFlashcardDeck(['name' => 'PieS Kot', 'user_id' => $user->id]),
    ]);

    $flashcards_to_one_deck = $this->generateFlashcardsForDecks($decks_to_merge);
    $flashcards_not_to_one_deck = $this->generateFlashcardsForDecks($decks_not_to_merge);

    $expected_deck = $decks_to_merge[0];

    // WHEN
    $this->artisan(MergeFlashcardsWithSameDeckCommand::class);

    // THEN
    $this->assertFlashcardsHasNewDeck($flashcards_to_one_deck, $expected_deck);
    $this->assertFlashcardsHasSameDeck($flashcards_not_to_one_deck);
    $this->assertFlashcardsDecksDeleted($decks_to_merge->skip(1));
});
test('handle merge flashcard to same user', function () {
    // GIVEN
    $user = $this->createUser();
    $decks_to_merge = collect([
        $this->createFlashcardDeck(['name' => 'pies kot', 'user_id' => $user->id]),
        $this->createFlashcardDeck(['name' => 'pies kot', 'user_id' => $user->id]),
    ]);
    $deck_not_to_merge = $this->createFlashcardDeck(['name' => 'pies kot']);
    $flashcards_to_one_deck = $this->generateFlashcardsForDecks($decks_to_merge);
    $flashcard_not_to_merge = $this->generateFlashcardsForDecks([$deck_not_to_merge])[0];
    $expected_deck = $decks_to_merge[0];

    // WHEN
    $this->artisan(MergeFlashcardsWithSameDeckCommand::class);

    // THEN
    $this->assertFlashcardsHasNewDeck($flashcards_to_one_deck, $expected_deck);
    $this->assertFlashcardsDecksDeleted($decks_to_merge->skip(1));
    $this->assertFlashcardsHasSameDeck([$flashcard_not_to_merge]);
});
