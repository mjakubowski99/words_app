<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\StoryRepository;

use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\User;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Enum\LanguageLevel;
use Shared\Models\Emoji;
use Shared\Utils\ValueObjects\Language;
use Shared\Utils\ValueObjects\StoryId;

trait StoryRepositoryTrait
{
    private function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }

    private function createFlashcardDeck(array $attributes = []): FlashcardDeck
    {
        return FlashcardDeck::factory()->create($attributes);
    }

    private function createStory(array $attributes = []): \App\Models\Story
    {
        return \App\Models\Story::factory()->create($attributes);
    }

    private function createNewStoryFlashcard(User $user, FlashcardDeck $deck, string $word, string $word_translation, string $sentence, ?string $sentence_override = null): StoryFlashcard
    {
        return new StoryFlashcard(
            StoryId::noId(),
            1,
            $sentence_override,
            new \Flashcard\Domain\Models\Flashcard(
                new FlashcardId(0),
                $word,
                Language::pl(),
                $word_translation,
                Language::en(),
                'context',
                $sentence,
                $user->toOwner(),
                $deck->toDomainModel(),
                LanguageLevel::A1,
                new Emoji('😀')
            ),
        );
    }

    private function createStoryWithFlashcard(\App\Models\Story $story, Flashcard $flashcard, ?string $sentence_override = null): \App\Models\StoryFlashcard
    {
        return \App\Models\StoryFlashcard::factory()->create([
            'story_id' => $story->id,
            'flashcard_id' => $flashcard->id,
            'sentence_override' => $sentence_override,
        ]);
    }
}
