<?php

declare(strict_types=1);

use App\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Infrastructure\Repositories\WordMatchExerciseReadRepository;
use Tests\Unit\Exercise\Infrastructure\Repository\WordMatchExerciseReadRepository\WordMatchExerciseReadRepositoryTrait;

uses(WordMatchExerciseReadRepositoryTrait::class);

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->repository = app(WordMatchExerciseReadRepository::class);
});

test('find by entry id existing entry returns exercise with entries', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createWordMatchExercise($user->getId());
    $entry_id = $exercise->getExerciseEntries()[0]->getId();

    // WHEN
    $found_exercise = $this->repository->findByEntryId($entry_id);

    // THEN
    expect($found_exercise->getExerciseId())->toEqual($exercise->getId());
    expect($found_exercise->isStory())->toBeFalse();

    $entry = $exercise->getExerciseEntries()[0];
    $found_entry = $found_exercise->getEntries()[0];

    expect($found_entry->getExerciseEntryId())->toEqual($entry->getId());
    expect($found_entry->getWord())->toEqual($entry->getWord());
    expect($found_entry->getWordTranslation())->toEqual($entry->getWordTranslation());
    expect($found_entry->getSentence())->toEqual($entry->getSentence());
});

test('find by entry id exercise with multiple entries returns all entries', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createWordMatchExerciseWithMultipleEntries($user->getId());
    $entry_id = $exercise->getExerciseEntries()[0]->getId();

    // WHEN
    $found_exercise = $this->repository->findByEntryId($entry_id);

    // THEN
    expect($found_exercise->getExerciseId())->toEqual($exercise->getId());
    expect($found_exercise->getEntries())->toHaveCount(3);

    foreach ($exercise->getExerciseEntries() as $i => $entry) {
        $found_entry = $found_exercise->getEntries()[$i];

        expect($found_entry->getExerciseEntryId())->toEqual($entry->getId());
        expect($found_entry->getWord())->toEqual($entry->getWord());
        expect($found_entry->getWordTranslation())->toEqual($entry->getWordTranslation());
        expect($found_entry->getSentence())->toEqual($entry->getSentence());
    }
});

test('find by entry id exercise with story id returns exercise with story flag', function () {
    // GIVEN
    $user = $this->createUser();
    $story = $this->createStory();
    $exercise = $this->createWordMatchExerciseWithStory(new StoryId($story->id), $user->getId());
    $entry_id = $exercise->getExerciseEntries()[0]->getId();

    // WHEN
    $found_exercise = $this->repository->findByEntryId($entry_id);

    // THEN
    expect($found_exercise->isStory())->toBeTrue();
});

test('answered is true when last answer is not null', function () {
    // GIVEN
    $user = $this->createUser();
    $story = $this->createStory();
    $exercise = $this->createWordMatchExerciseWithStory(new StoryId($story->id), $user->getId());
    $entry_id = $exercise->getExerciseEntries()[0]->getId();
    ExerciseEntry::find($entry_id->getValue())->update(['last_answer' => 'answered']);

    // WHEN
    $found_exercise = $this->repository->findByEntryId($entry_id);

    // THEN
    expect($found_exercise->getEntries()[0]->isAnswered())->toBeTrue();
});

test('is answered value is false when no answer', function () {
    // GIVEN
    $user = $this->createUser();
    $story = $this->createStory();
    $exercise = $this->createWordMatchExerciseWithStory(new StoryId($story->id), $user->getId());
    $entry_id = $exercise->getExerciseEntries()[0]->getId();
    ExerciseEntry::find($entry_id->getValue())->update(['last_answer' => null]);

    // WHEN
    $found_exercise = $this->repository->findByEntryId($entry_id);

    // THEN
    expect($found_exercise->getEntries()[0]->isAnswered())->toBeFalse();
});

test('find by entry id non existent entry throws exception', function () {
    // GIVEN
    $non_existent_id = new ExerciseEntryId(10000);

    // THEN
    $this->expectException(Exception::class);

    // WHEN
    $this->repository->findByEntryId($non_existent_id);
});
