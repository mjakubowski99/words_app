<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\FlashcardDuplicateRepository;

use Tests\TestCase;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDuplicateRepository;

class FlashcardDuplicateRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private FlashcardDuplicateRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(FlashcardDuplicateRepository::class);
    }

    /**
     * @dataProvider savedWordsDataProvider
     */
    public function test__getAlreadySavedWords_ShouldReturnAlreadySavedWordsFromArray(
        array $saved_words,
        array $new_words,
        array $expected,
    ): void {
        // GIVEN
        $deck = FlashcardDeck::factory()->create();
        foreach ($saved_words as $saved_word) {
            Flashcard::factory()->create(['flashcard_deck_id' => $deck->id, 'front_word' => $saved_word]);
        }

        // WHEN
        $results = $this->repository->getAlreadySavedFrontWords($deck->getId(), $new_words);

        // THEN
        $this->assertArraysAreTheSame($expected, $results);
    }

    /**
     * @dataProvider initialLetterWordsDataProvider
     */
    public function test__getRandomFrontWordInitialLetters_ShouldSaveFirstLetters(
        array $saved_words,
        array $expected,
    ): void {
        // GIVEN
        $deck = FlashcardDeck::factory()->create();
        foreach ($saved_words as $saved_word) {
            Flashcard::factory()->create(['flashcard_deck_id' => $deck->id, 'front_word' => $saved_word]);
        }

        // WHEN
        $results = $this->repository->getRandomFrontWordInitialLetters($deck->getId(), 10);

        // THEN
        $this->assertArraysAreTheSame($expected, $results);
    }

    public static function savedWordsDataProvider(): \Generator
    {
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
    }

    public static function initialLetterWordsDataProvider(): \Generator
    {
        yield 'set 1' => [
            'saved_words' => ['apple', 'game', 'idk', 'grammar', 'open'],
            'expected' => ['a', 'g', 'i', 'o'],
        ];
    }
}
