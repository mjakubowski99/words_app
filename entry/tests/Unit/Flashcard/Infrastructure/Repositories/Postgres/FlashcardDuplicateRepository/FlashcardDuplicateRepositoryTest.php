<?php

declare(strict_types=1);
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDuplicateRepository;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = $this->app->make(FlashcardDuplicateRepository::class);
});
test('get already saved words should return already saved words from array', function (array $saved_words, array $new_words, array $expected) {
    // GIVEN
    $deck = FlashcardDeck::factory()->create();
    foreach ($saved_words as $saved_word) {
        Flashcard::factory()->create(['flashcard_deck_id' => $deck->id, 'front_word' => $saved_word]);
    }

    // WHEN
    $results = $this->repository->getAlreadySavedFrontWords($deck->getId(), $new_words);

    // THEN
    $this->assertArraysAreTheSame($expected, $results);
})->with('savedWordsDataProvider');
dataset('savedWordsDataProvider', function () {
    yield 'set 1' => [
        'saved_words' => ['apple', 'pen', 'girl'],
        'new_words' => ['grump', 'girl'],
        'expected' => ['girl'],
    ];

    yield 'set 2' => [
        'saved_words' => ['name', 'game', 'end'],
        'new_words' => ['Name', 'game', 'End'],
        'expected' => ['name', 'game', 'end'],
    ];
});
test('get random front word initial letters should save first letters', function (array $saved_words, array $expected) {
    // GIVEN
    $deck = FlashcardDeck::factory()->create();
    foreach ($saved_words as $saved_word) {
        Flashcard::factory()->create(['flashcard_deck_id' => $deck->id, 'front_word' => $saved_word]);
    }

    // WHEN
    $results = $this->repository->getRandomFrontWordInitialLetters($deck->getId(), 10);

    // THEN
    $this->assertArraysAreTheSame($expected, $results);
})->with('initialLetterWordsDataProvider');
dataset('initialLetterWordsDataProvider', function () {
    yield 'set 1' => [
        'saved_words' => ['apple', 'game', 'idk', 'grammar', 'open'],
        'expected' => ['a', 'g', 'i', 'o'],
    ];
});
