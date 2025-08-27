<?php

declare(strict_types=1);
use Flashcard\Domain\Services\FlashcardDuplicateService;
use Flashcard\Application\Services\StoryDuplicateService;
use Tests\Unit\Flashcard\Application\StoryDuplicateService\StoryDuplicateServiceTrait;

uses(StoryDuplicateServiceTrait::class);

beforeEach(function () {
    $this->duplicate_service = $this->mockery(FlashcardDuplicateService::class);
    $this->service = $this->app->make(StoryDuplicateService::class, [
        'duplicate_service' => $this->duplicate_service,
    ]);
});
test('remove duplicates when duplicates exist should remove stories with duplicates', function () {
    // GIVEN
    $resolved_deck = $this->createResolvedDeck();
    $stories = $this->createStoriesWithDuplicates();
    $this->mockDuplicateService($stories, 2);

    // WHEN
    $stories = $this->service->removeDuplicates($resolved_deck, $stories, 5);

    // THEN
    expect($stories->getPulledFlashcards())->toHaveCount(2);
    expect($stories->get())->toHaveCount(0);
    expect($stories->getPulledFlashcards()[0]->getFrontWord())->toBe('word1');
    expect($stories->getPulledFlashcards()[1]->getFrontWord())->toBe('word2');
});
test('remove duplicates when no duplicates in one story and duplicates in second story should remove stories with duplicates', function () {
    // GIVEN
    $resolved_deck = $this->createResolvedDeck();
    $stories = $this->createStoriesWithNoDuplicatesAndDuplicates();
    $this->mockNoDuplicatesAndDuplicate($stories, 2);

    // WHEN
    $stories = $this->service->removeDuplicates($resolved_deck, $stories, 5);

    // THEN
    expect($stories->getPulledFlashcards())->toHaveCount(2);
    expect($stories->get())->toHaveCount(1);
    expect($stories->getPulledFlashcards()[0]->getFrontWord())->toBe('word4');
    expect($stories->getPulledFlashcards()[1]->getFrontWord())->toBe('word3');
});
test('remove duplicates when no changes should keep stories intact', function () {
    // GIVEN
    $resolved_deck = $this->createResolvedDeck();
    $stories = $this->createStoriesWithoutDuplicates();
    $this->mockDuplicateService($stories, 3);

    // WHEN
    $stories = $this->service->removeDuplicates($resolved_deck, $stories, 3);

    // THEN
    expect($stories->getPulledFlashcards())->toBeEmpty();
    expect($stories->get())->toHaveCount(1);
    expect($stories->get()[0]->getStoryFlashcards())->toHaveCount(3);
});
test('remove duplicates when limit reached should truncate results', function () {
    // GIVEN
    $resolved_deck = $this->createResolvedDeck();
    $stories = $this->createStoriesWithDuplicates();
    $this->mockDuplicateService($stories, 2);

    // WHEN
    $stories = $this->service->removeDuplicates($resolved_deck, $stories, 1);

    // THEN
    expect($stories->getPulledFlashcards())->toHaveCount(1);
    expect($stories->get())->toHaveCount(0);
    expect($stories->getPulledFlashcards()[0]->getFrontWord())->toBe('word1');
});
