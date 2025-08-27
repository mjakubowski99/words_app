<?php

declare(strict_types=1);
use Flashcard\Domain\Models\Story;
use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\StoryFlashcard;
use Flashcard\Domain\Models\StoryCollection;

test('pull stories with only one sentence should remove stories with one sentence', function () {
    // GIVEN
    $flashcard1 = Mockery::mock(Flashcard::class);
    $story_flashcard1 = Mockery::mock(StoryFlashcard::class);
    $story_flashcard1->shouldReceive('getFlashcard')->andReturn($flashcard1);

    $story1 = Mockery::mock(Story::class);
    $story1->shouldReceive('getStoryFlashcards')->andReturn([$story_flashcard1]);

    $story2 = Mockery::mock(Story::class);
    $story2->shouldReceive('getStoryFlashcards')->andReturn([
        Mockery::mock(StoryFlashcard::class),
        Mockery::mock(StoryFlashcard::class),
    ]);

    $collection = new StoryCollection([$story1, $story2]);

    // WHEN
    $collection->pullStoriesWithOnlyOneSentence();

    // THEN
    expect($collection->get())->toHaveCount(1);
    expect($collection->getPulledFlashcards()[0])->toBe($flashcard1);
});
test('pull stories with only one sentence when no stories with one sentence should not remove any stories', function () {
    // GIVEN
    $story1 = Mockery::mock(Story::class);
    $story1->shouldReceive('getStoryFlashcards')->andReturn([
        Mockery::mock(StoryFlashcard::class),
        Mockery::mock(StoryFlashcard::class),
    ]);

    $story2 = Mockery::mock(Story::class);
    $story2->shouldReceive('getStoryFlashcards')->andReturn([
        Mockery::mock(StoryFlashcard::class),
        Mockery::mock(StoryFlashcard::class),
    ]);

    $collection = new StoryCollection([$story1, $story2]);

    // WHEN
    $collection->pullStoriesWithOnlyOneSentence();

    // THEN
    expect($collection->get())->toHaveCount(2);
    expect($collection->getPulledFlashcards())->toBeEmpty();
});
