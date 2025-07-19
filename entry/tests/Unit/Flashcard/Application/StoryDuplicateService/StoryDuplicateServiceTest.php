<?php

declare(strict_types=1);

namespace Tests\Unit\Flashcard\Application\StoryDuplicateService;

use Tests\TestCase;
use Mockery\MockInterface;
use Flashcard\Domain\Services\FlashcardDuplicateService;
use Flashcard\Application\Services\StoryDuplicateService;

class StoryDuplicateServiceTest extends TestCase
{
    use StoryDuplicateServiceTrait;

    private StoryDuplicateService $service;
    private FlashcardDuplicateService|MockInterface $duplicate_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->duplicate_service = $this->mockery(FlashcardDuplicateService::class);
        $this->service = $this->app->make(StoryDuplicateService::class, [
            'duplicate_service' => $this->duplicate_service,
        ]);
    }

    public function test__removeDuplicates_WhenDuplicatesExist_ShouldRemoveStoriesWithDuplicates(): void
    {
        // GIVEN
        $resolved_deck = $this->createResolvedDeck();
        $stories = $this->createStoriesWithDuplicates();
        $this->mockDuplicateService($stories, 2);

        // WHEN
        $result = $this->service->removeDuplicates($resolved_deck, $stories, 5);

        // THEN
        $this->assertCount(2, $result);
        $this->assertCount(0, $stories->get());
        $this->assertSame('word1', $result[0]->getFrontWord());
        $this->assertSame('word2', $result[1]->getFrontWord());
    }

    public function test__removeDuplicates_WhenNoDuplicatesInOneStoryAndDuplicatesInSecondStory_ShouldRemoveStoriesWithDuplicates(): void
    {
        // GIVEN
        $resolved_deck = $this->createResolvedDeck();
        $stories = $this->createStoriesWithNoDuplicatesAndDuplicates();
        $this->mockNoDuplicatesAndDuplicate($stories, 2);

        // WHEN
        $result = $this->service->removeDuplicates($resolved_deck, $stories, 5);

        // THEN
        $this->assertCount(2, $result);
        $this->assertCount(1, $stories->get());
        $this->assertSame('word4', $result[0]->getFrontWord());
        $this->assertSame('word3', $result[1]->getFrontWord());
    }

    public function test__removeDuplicates_WhenNoChanges_ShouldKeepStoriesIntact(): void
    {
        // GIVEN
        $resolved_deck = $this->createResolvedDeck();
        $stories = $this->createStoriesWithoutDuplicates();
        $this->mockDuplicateService($stories, 3);

        // WHEN
        $result = $this->service->removeDuplicates($resolved_deck, $stories, 3);

        // THEN
        $this->assertEmpty($result);
        $this->assertCount(1, $stories->get());
        $this->assertCount(3, $stories->get()[0]->getStoryFlashcards());
    }

    public function test__removeDuplicates_WhenLimitReached_ShouldTruncateResults(): void
    {
        // GIVEN
        $resolved_deck = $this->createResolvedDeck();
        $stories = $this->createStoriesWithDuplicates();
        $this->mockDuplicateService($stories, 2);

        // WHEN
        $result = $this->service->removeDuplicates($resolved_deck, $stories, 1);

        // THEN
        $this->assertCount(1, $result);
        $this->assertCount(0, $stories->get());
        $this->assertSame('word1', $result[0]->getFrontWord());
    }
}
