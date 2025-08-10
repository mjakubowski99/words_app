<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Domain\Models\StoryCollection;

use Tests\TestCase;
use Flashcard\Domain\Models\Story;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Models\StoryCollection;

class StoryCollectionTest extends TestCase
{
    public function test__pullStoriesWithOnlyOneSentence_ShouldRemoveStoriesWithOneSentence(): void
    {
        // GIVEN
        $flashcard1 = \Mockery::mock(Flashcard::class);
        $story_flashcard1 = \Mockery::mock(StoryFlashcard::class);
        $story_flashcard1->shouldReceive('getFlashcard')->andReturn($flashcard1);

        $story1 = \Mockery::mock(Story::class);
        $story1->shouldReceive('getStoryFlashcards')->andReturn([$story_flashcard1]);

        $story2 = \Mockery::mock(Story::class);
        $story2->shouldReceive('getStoryFlashcards')->andReturn([
            \Mockery::mock(StoryFlashcard::class),
            \Mockery::mock(StoryFlashcard::class),
        ]);

        $collection = new StoryCollection([$story1, $story2]);

        // WHEN
        $collection->pullStoriesWithOnlyOneSentence();

        // THEN
        $this->assertCount(1, $collection->get());
        $this->assertSame($flashcard1, $collection->getPulledFlashcards()[0]);
    }

    public function test__pullStoriesWithOnlyOneSentence_WhenNoStoriesWithOneSentence_ShouldNotRemoveAnyStories(): void
    {
        // GIVEN
        $story1 = \Mockery::mock(Story::class);
        $story1->shouldReceive('getStoryFlashcards')->andReturn([
            \Mockery::mock(StoryFlashcard::class),
            \Mockery::mock(StoryFlashcard::class),
        ]);

        $story2 = \Mockery::mock(Story::class);
        $story2->shouldReceive('getStoryFlashcards')->andReturn([
            \Mockery::mock(StoryFlashcard::class),
            \Mockery::mock(StoryFlashcard::class),
        ]);

        $collection = new StoryCollection([$story1, $story2]);

        // WHEN
        $collection->pullStoriesWithOnlyOneSentence();

        // THEN
        $this->assertCount(2, $collection->get());
        $this->assertEmpty($collection->getPulledFlashcards());
    }
}
