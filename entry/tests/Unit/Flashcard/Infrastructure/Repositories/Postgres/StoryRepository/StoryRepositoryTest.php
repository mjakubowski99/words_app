<?php

declare(strict_types=1);

use App\Models\StoryFlashcard;
use Flashcard\Domain\Models\Story;
use Shared\Utils\ValueObjects\StoryId;
use Flashcard\Domain\Models\StoryCollection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Flashcard\Infrastructure\Repositories\Postgres\StoryRepository;
use Tests\Unit\Flashcard\Infrastructure\Repositories\Postgres\StoryRepository\StoryRepositoryTrait;

uses(DatabaseTransactions::class);
uses(StoryRepositoryTrait::class);

beforeEach(function () {
    $this->repository = app()->make(StoryRepository::class);
});

test('find random story id by flashcard id when story exists returns story', function () {
    // GIVEN
    $user = $this->createUser();
    $story = $this->createStory();
    $flashcard = $this->createFlashcard([
        'front_word' => 'test',
        'user_id' => $user->id,
    ]);
    $this->createStoryWithFlashcard($story, $flashcard, 'Test sentence');

    // WHEN
    $result = $this->repository->findRandomStoryIdByFlashcard($flashcard->getId());

    // THEN
    expect($result)->toBeInstanceOf(StoryId::class);
    expect($result->getValue())->toBe($story->id);
});
test('find when story exists returns story', function () {
    // GIVEN
    $user = $this->createUser();
    $story = $this->createStory();
    $flashcard = $this->createFlashcard([
        'front_word' => 'test',
        'user_id' => $user->id,
    ]);
    $this->createStoryWithFlashcard($story, $flashcard, 'Test sentence');

    // WHEN
    $result = $this->repository->find(new StoryId($story->id), $user->getId());

    // THEN
    expect($result)->toBeInstanceOf(Story::class);
    expect($result->getId()->getValue())->toBe($story->id);
    expect($result->getStoryFlashcards()[0]->getFlashcard()->getFrontWord())->toBe($flashcard->front_word);
    expect($result->getStoryFlashcards()[0]->getFlashcard()->getBackWord())->toBe($flashcard->back_word);
});
test('save many creates stories with flashcards', function () {
    // GIVEN
    $user = $this->createUser();
    $deck = $this->createFlashcardDeck([
        'user_id' => $user->id,
    ]);
    $stories = [];

    $story_flashcards = [
        $this->createNewStoryFlashcard($user, $deck, 'word', 'translation', 'Sentence'),
        $this->createNewStoryFlashcard($user, $deck, 'word 2', 'translation 2', 'Sentence 2'),
    ];
    $stories[] = new Story(
        StoryId::noId(),
        $story_flashcards
    );

    $story_flashcards = [
        $this->createNewStoryFlashcard($user, $deck, 'wx', 't', 'Se'),
        $this->createNewStoryFlashcard($user, $deck, 'w2', 't2', 'Sen'),
    ];
    $stories[] = new Story(
        StoryId::noId(),
        $story_flashcards
    );

    // WHEN
    $this->repository->saveMany(new StoryCollection($stories));

    // THEN
    $stories = App\Models\Story::all();
    expect($stories)->toHaveCount(2);

    $story_flashcards = StoryFlashcard::query()->where('story_id', $stories[0]->id)->get();

    expect($story_flashcards[0]->flashcard->front_word)->toBe('word');
    expect($story_flashcards[1]->flashcard->front_word)->toBe('word 2');

    $story_flashcards = StoryFlashcard::query()->where('story_id', $stories[1]->id)->get();

    expect($story_flashcards[0]->flashcard->front_word)->toBe('wx');
    expect($story_flashcards[1]->flashcard->front_word)->toBe('w2');
});
test('save many when sentence override saves with sentence override', function () {
    // GIVEN
    $user = $this->createUser();
    $deck = $this->createFlashcardDeck([
        'user_id' => $user->id,
    ]);

    $story_flashcards = [
        $this->createNewStoryFlashcard($user, $deck, 'word', 'translation', 'Sentence', 'Override'),
    ];
    $story = new Story(
        StoryId::noId(),
        $story_flashcards
    );

    // WHEN
    $this->repository->saveMany(new StoryCollection([$story]));

    // THEN
    $stories = App\Models\Story::all();
    expect($stories)->toHaveCount(1);

    $story_flashcards = StoryFlashcard::query()->where('story_id', $stories[0]->id)->get();

    expect($story_flashcards[0]->sentence_override)->toBe('Override');
});
test('bulk delete delete stories', function () {
    // GIVEN
    $to_delete = [
        StoryFlashcard::factory()->create(),
        StoryFlashcard::factory()->create(),
    ];
    $to_keep = StoryFlashcard::factory()->create();

    // WHEN
    $this->repository->bulkDelete([new StoryId($to_delete[0]->story_id), new StoryId($to_delete[1]->story_id)]);

    // THEN
    $this->assertDatabaseMissing('story_flashcards', ['story_id' => $to_delete[0]->story_id]);
    $this->assertDatabaseMissing('story_flashcards', ['story_id' => $to_delete[1]->story_id]);
    $this->assertDatabaseHas('stories', ['id' => $to_keep->story_id]);
    $this->assertDatabaseHas('flashcards', ['id' => $to_delete[0]->flashcard_id]);
    $this->assertDatabaseHas('flashcards', ['id' => $to_delete[1]->flashcard_id]);
});
