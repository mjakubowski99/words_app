<?php

declare(strict_types=1);

use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Story;
use Shared\Utils\ValueObjects\StoryId;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Models\StoryCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Domain\Services\FlashcardDuplicateService;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;
use Tests\Integration\Flashcards\Application\Services\FlashcardDuplicateService\FlashcardDuplicateServiceTrait;

uses(FlashcardTestCase::class);
uses(DatabaseTransactions::class);
uses(FlashcardDuplicateServiceTrait::class);

beforeEach(function () {
    $this->repository = $this->mockery(IFlashcardDuplicateRepository::class);
    $this->service = $this->app->make(FlashcardDuplicateService::class, [
        'duplicate_repository' => $this->repository,
    ]);
});

test('remove duplicates should remove duplicates', function (array $saved_words, array $new_words, array $expected) {
    // GIVEN
    $deck = $this->createDeck();
    $this->mockSavedWordsRepository($saved_words);
    $flashcards = $this->makeFlashcards($deck, $new_words);

    // WHEN
    $flashcards = $this->service->removeDuplicates($deck, new StoryCollection([
        new Story(StoryId::noId(), $flashcards),
    ]));

    // THEN
    $front_words = array_map(fn (StoryFlashcard $flashcard) => $flashcard->getFlashcard()->getFrontWord(), $flashcards);

    $this->assertArraysAreTheSame($expected, $front_words);
})->with('dataProvider');

dataset('dataProvider', function () {
    yield 'set 1' => [
        'saved_words' => ['jablko', 'Banan'],
        'new_words' => ['parowki', 'jablko'],
        'expected' => ['parowki'],
    ];

    yield 'different character case' => [
        'saved_words' => ['arbuz', 'owocE'],
        'new_words' => ['owoce', 'grzanki', 'Arbuz'],
        'expected' => ['grzanki'],
    ];

    yield 'duplicate in new words' => [
        'saved_words' => [],
        'new_words' => ['gruszka', 'jablko', 'banan', 'jablko', 'banan', 'gruszka'],
        'expected' => ['gruszka', 'jablko', 'banan'],
    ];

    yield 'duplicate in new words different character case' => [
        'saved_words' => [],
        'new_words' => ['Gra', 'gra'],
        'expected' => ['Gra'],
    ];
});
