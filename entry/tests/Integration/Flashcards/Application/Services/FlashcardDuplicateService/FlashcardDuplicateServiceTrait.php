<?php

declare(strict_types=1);

namespace Tests\Integration\Flashcards\Application\Services\FlashcardDuplicateService;

use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardId;

trait FlashcardDuplicateServiceTrait
{
    public function mockSavedWordsRepository(array $saved_words): void
    {
        $this->repository->shouldReceive('getAlreadySavedFrontWords')->andReturn($saved_words);
    }

    /** @return StoryFlashcard[] */
    public function makeFlashcards(Deck $deck, array $front_words): array
    {
        $flashcards = [];
        foreach ($front_words as $front_word) {
            $flashcards[] = new StoryFlashcard(
                StoryId::noId(),
                1,
                null,
                new Flashcard(
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
                ),
            );
        }

        return $flashcards;
    }

    public function createDeck(): Deck
    {
        $deck = FlashcardDeck::factory()->create();

        return $deck->toDomainModel();
    }
}
