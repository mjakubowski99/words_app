<?php

declare(strict_types=1);

use Tests\Base\FlashcardTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardRepository::class);
});

test('replace deck replace deck for correct flashcards', function () {
    // GIVEN
    $actual_deck = $this->createFlashcardDeck();
    $flashcards = [
        $this->createFlashcard(['flashcard_deck_id' => $actual_deck->id]),
        $this->createFlashcard(['flashcard_deck_id' => $actual_deck->id]),
    ];
    $other_flashcard = $this->createFlashcard();
    $new_deck = $this->createFlashcardDeck();

    // WHEN
    $this->repository->replaceDeck($actual_deck->getId(), $new_deck->getId());

    // THEN
    foreach ($flashcards as $flashcard) {
        $this->assertDatabaseHas('flashcards', [
            'id' => $flashcard->id,
            'flashcard_deck_id' => $new_deck->id,
        ]);
    }
    $this->assertDatabaseHas('flashcards', [
        'id' => $other_flashcard->id,
        'flashcard_deck_id' => $other_flashcard->flashcard_deck_id,
    ]);
});
