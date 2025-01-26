<?php

declare(strict_types=1);

namespace Integration\Flashcards\Application\Services\FlashcardDuplicateService;

use Mockery\MockInterface;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Tests\Base\FlashcardTestCase;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Domain\Services\FlashcardDuplicateService;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;

class FlashcardDuplicateServiceTest extends FlashcardTestCase
{
    use DatabaseTransactions;

    private FlashcardDuplicateService $service;

    private IFlashcardDuplicateRepository|MockInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->mockery(IFlashcardDuplicateRepository::class);
        $this->service = $this->app->make(FlashcardDuplicateService::class, [
            'duplicate_repository' => $this->repository,
        ]);
    }

    /**
     * @dataProvider dataProvider
     */
    public function test__removeDuplicates_ShouldRemoveDuplicates(
        array $saved_words,
        array $new_words,
        array $expected
    ): void {
        // GIVEN
        $deck = $this->createDeck();
        $this->mockSavedWordsRepository($saved_words);
        $flashcards = $this->makeFlashcards($deck, $new_words);

        // WHEN
        $flashcards = $this->service->removeDuplicates($deck, $flashcards);

        // THEN
        $front_words = array_map(fn (Flashcard $flashcard) => $flashcard->getFrontWord(), $flashcards);
        $this->assertArraysAreTheSame($expected, $front_words);
    }

    public static function dataProvider(): \Generator
    {
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
    }

    private function mockSavedWordsRepository(array $saved_words): void
    {
        $this->repository->shouldReceive('getAlreadySavedFrontWords')->andReturn($saved_words);
    }

    /** @return Flashcard[] */
    private function makeFlashcards(Deck $deck, array $front_words): array
    {
        $flashcards = [];
        foreach ($front_words as $front_word) {
            $flashcards[] = new Flashcard(
                FlashcardId::noId(),
                $front_word,
                Language::pl(),
                'back',
                Language::en(),
                'context',
                'back context',
                $deck->getOwner(),
                $deck,
                LanguageLevel::B2,
                null,
            );
        }

        return $flashcards;
    }

    private function createDeck(): Deck
    {
        $deck = FlashcardDeck::factory()->create();

        return $deck->toDomainModel();
    }
}
