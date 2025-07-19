<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Application\StoryDuplicateService;

use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Story;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Application\DTO\ResolvedDeck;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Models\StoryCollection;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\Language;
use Shared\Enum\LanguageLevel;

trait StoryDuplicateServiceTrait
{
    private function createResolvedDeck(): ResolvedDeck
    {
        $user_id = UserId::new();
        $deck = new Deck(
            Owner::fromUser($user_id),
            'tag',
            'Test Deck',
            LanguageLevel::A1,
        );

        return new ResolvedDeck(false, $deck);
    }

    private function createStoriesWithDuplicates(): StoryCollection
    {
        $story = new Story(StoryId::noId(), [
            $this->createStoryFlashcard('word1', 0),
            $this->createStoryFlashcard('word2', 0),
            $this->createStoryFlashcard('word1', 0),
        ]);

        return new StoryCollection([$story]);
    }

    private function createStoriesWithNoDuplicatesAndDuplicates(): StoryCollection
    {
        $story_1 = new Story(StoryId::noId(), [
            $this->createStoryFlashcard('word1', 0),
            $this->createStoryFlashcard('word2', 0),
            $this->createStoryFlashcard('word3', 0),
        ]);
        $story_2 = new Story(StoryId::noId(), [
            $this->createStoryFlashcard('word4', 1),
            $this->createStoryFlashcard('word3', 1),
            $this->createStoryFlashcard('word4', 1),
        ]);

        return new StoryCollection([$story_1, $story_2]);
    }

    private function createStoriesWithoutDuplicates(): StoryCollection
    {
        $story = new Story(StoryId::noId(), [
            $this->createStoryFlashcard('unique1', 0),
            $this->createStoryFlashcard('unique2', 0),
            $this->createStoryFlashcard('unique3', 0),
        ]);

        return new StoryCollection([$story]);
    }

    private function mockDuplicateService(StoryCollection $stories, int $count): array
    {
        $filtered_flashcards = array_slice(iterator_to_array($stories->getAllStoryFlashcards()), 0, $count);

        $this->duplicate_service
            ->shouldReceive('removeDuplicates')
            ->once()
            ->andReturn($filtered_flashcards);

        return $filtered_flashcards;
    }

    private function mockNoDuplicatesAndDuplicate(StoryCollection $stories, int $count): array
    {
        $filtered_flashcards = array_slice(iterator_to_array($stories->getAllStoryFlashcards()), 0, $count);

        $this->duplicate_service
            ->shouldReceive('removeDuplicates')
            ->andReturnValues([iterator_to_array($stories->getAllStoryFlashcards()), $filtered_flashcards]);

        return $filtered_flashcards;
    }

    private function createStoryFlashcard(string $word, int $index): StoryFlashcard
    {
        $flashcard = \Mockery::mock(Flashcard::class);
        $flashcard->allows([
            'getFrontWord' => $word,
            'getId' => FlashcardId::noId(),
        ]);

        return new StoryFlashcard(StoryId::noId(), $index, null, $flashcard);
    }
}
