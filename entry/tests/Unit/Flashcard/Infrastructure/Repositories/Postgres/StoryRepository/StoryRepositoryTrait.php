<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\StoryRepository;

use App\Models\User;
use App\Models\Story;
use Shared\Models\Emoji;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Shared\Enum\LanguageLevel;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardId;

trait StoryRepositoryTrait
{
    public function createFlashcard(array $attributes = []): Flashcard
    {
        return Flashcard::factory()->create($attributes);
    }

    public function createFlashcardDeck(array $attributes = []): FlashcardDeck
    {
        return FlashcardDeck::factory()->create($attributes);
    }

    public function createStory(array $attributes = []): Story
    {
        return Story::factory()->create($attributes);
    }

    public function createNewStoryFlashcard(User $user, FlashcardDeck $deck, string $word, string $word_translation, string $sentence, ?string $sentence_override = null): StoryFlashcard
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

    public function createStoryWithFlashcard(Story $story, Flashcard $flashcard, ?string $sentence_override = null): \App\Models\StoryFlashcard
    {
        return \App\Models\StoryFlashcard::factory()->create([
            'story_id' => $story->id,
            'flashcard_id' => $flashcard->id,
            'sentence_override' => $sentence_override,
        ]);
    }
}
