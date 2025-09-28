<?php

declare(strict_types=1);

use App\Models\Exercise;
use App\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\StoryId;
use Exercise\Domain\Models\ExerciseStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Exercise\Infrastructure\Repositories\WordMatchExerciseRepository;
use Tests\Unit\Exercise\Infrastructure\Repository\WordMatchExerciseRepository\WordMatchExerciseRepositoryTrait;

uses(DatabaseTransactions::class);

uses(WordMatchExerciseRepositoryTrait::class);

beforeEach(function () {
    ExerciseEntry::query()->forceDelete();
    Exercise::query()->forceDelete();
    $this->repository = $this->app->make(WordMatchExerciseRepository::class);
});
test('find existing exercise returns exercise with entries', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createWordMatchExercise($user->getId());
    $this->repository->create($exercise);

    // WHEN
    $found_exercise = $this->repository->find($exercise->getId());

    // THEN
    expect($found_exercise->getId())->toEqual($exercise->getId());
    expect($found_exercise->getUserId())->toEqual($exercise->getUserId());
    expect($found_exercise->getStatus())->toEqual($exercise->getStatus());

    $entry = $exercise->getExerciseEntries()[0];
    $found_entry = $found_exercise->getExerciseEntries()[0];

    expect($found_entry->getId())->toEqual($entry->getId());
    expect($found_entry->getWord())->toEqual($entry->getWord());
    expect($found_entry->getWordTranslation())->toEqual($entry->getWordTranslation());
    expect($found_entry->getSentence())->toEqual($entry->getSentence());
    expect($found_entry->getCorrectAnswer())->toEqual($entry->getCorrectAnswer());
});
test('find exercise with multiple entries returns all entries', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createWordMatchExerciseWithMultipleEntries($user->getId());
    $this->repository->create($exercise);

    // WHEN
    $found_exercise = $this->repository->find($exercise->getId());

    // THEN
    expect($found_exercise->getExerciseEntries())->toHaveCount(3);

    foreach ($exercise->getExerciseEntries() as $i => $entry) {
        $found_entry = $found_exercise->getExerciseEntries()[$i];

        expect($found_entry->getId())->toEqual($entry->getId());
        expect($found_entry->getWord())->toEqual($entry->getWord());
        expect($found_entry->getWordTranslation())->toEqual($entry->getWordTranslation());
        expect($found_entry->getSentence())->toEqual($entry->getSentence());
        expect($found_entry->getCorrectAnswer())->toEqual($entry->getCorrectAnswer());
    }
});
test('create valid exercise stores exercise with entries', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createNewWordMatchExercise($user->getId());

    // WHEN
    $this->repository->create($exercise);

    // THEN
    $this->assertDatabaseHas('exercises', [
        'exercise_type' => $exercise->getExerciseType()->toNumber(),
        'user_id' => $exercise->getUserId()->getValue(),
        'status' => ExerciseStatus::NEW->value,
    ]);

    $this->assertDatabaseHas('exercise_entries', [
        'correct_answer' => $exercise->getExerciseEntries()[0]->getCorrectAnswer()->toString(),
        'score' => 0.0,
        'answers_count' => 0,
        'order' => 0,
    ]);
});
test('create exercise with multiple entries stores all entries', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createNewWordMatchExerciseWithMultipleEntries($user->getId());

    // WHEN
    $this->repository->create($exercise);

    // THEN
    $this->assertDatabaseCount('exercise_entries', 3);
});
test('create exercise with multiple entries stores correct properties', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createNewWordMatchExerciseWithMultipleEntries($user->getId());

    // WHEN
    $this->repository->create($exercise);

    // THEN
    $exercise_db = Exercise::query()
        ->where('user_id', $exercise->getUserId()->getValue())
        ->first();

    expect($exercise_db)->not->toBeNull();
    $properties = json_decode($exercise_db->properties, true);

    expect($properties['sentences'])->toBeArray();
    expect($properties['sentences'])->toHaveCount(3);

    for ($i = 0; $i < 3; ++$i) {
        expect($properties['sentences'][$i]['order'])->toEqual($i);
        expect($properties['sentences'][$i]['word'])->toEqual("word{$i}");
        expect($properties['sentences'][$i]['translation'])->toEqual("translation{$i}");
        expect($properties['sentences'][$i]['sentence'])->toEqual("sentence{$i}");
    }

    $entries = ExerciseEntry::query()
        ->where('exercise_id', $exercise_db->id)
        ->orderBy('order')
        ->get();

    expect($entries)->toHaveCount(3);
    foreach ($entries as $i => $entry) {
        expect($entry->order)->toEqual($i);
        expect($entry->correct_answer)->toEqual("correct{$i}");
    }
});
test('create exercise with story id stores story id in properties', function () {
    // GIVEN
    $user = $this->createUser();
    $story = $this->createStory();
    $exercise = $this->createWordMatchExerciseWithStory(new StoryId($story->id), $user->getId());

    // WHEN
    $this->repository->create($exercise);

    // THEN
    $exercise_db = Exercise::query()
        ->where('user_id', $exercise->getUserId()->getValue())
        ->first();

    expect($exercise_db)->not->toBeNull();
    $properties = json_decode($exercise_db->properties, true);

    expect($properties)->toHaveKey('story_id');
    expect($properties['story_id'])->toEqual($exercise->getStoryId()->getValue());
    expect($properties['sentences'])->not->toBeEmpty();
});
test('save updated exercise updates exercise and entries', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createWordMatchExercise($user->getId());
    $this->repository->create($exercise);

    // WHEN
    $this->repository->save($exercise);

    // THEN
    $this->assertDatabaseHas('exercises', [
        'id' => $exercise->getId()->getValue(),
        'user_id' => $exercise->getUserId()->getValue(),
        'status' => $exercise->getStatus()->value,
    ]);
    $this->assertDatabaseHas('exercise_entries', [
        'id' => $exercise->getExerciseEntries()[0]->getId(),
        'correct_answer' => $exercise->getExerciseEntries()[0]->getCorrectAnswer()->toString(),
        'score' => 20.0,
        'answers_count' => 2,
        'order' => 0,
    ]);
});
test('save exercise with multiple entries updates all entries', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createWordMatchExerciseWithMultipleEntries($user->getId());
    $this->repository->create($exercise);

    // WHEN
    $this->repository->save($exercise);

    // THEN
    $entries = ExerciseEntry::query()
        ->where('exercise_id', $exercise->getId()->getValue())
        ->orderBy('order')
        ->get();

    expect($entries)->toHaveCount(3);
    foreach ($entries as $entry) {
        expect($entry->score)->toEqual(20.0);
        expect($entry->answers_count)->toEqual(2);
    }
});
test('save exercise with updated properties updates properties', function () {
    // GIVEN
    $user = $this->createUser();
    $exercise = $this->createWordMatchExerciseWithMultipleEntries($user->getId());
    $this->repository->create($exercise);

    // WHEN
    $this->repository->save($exercise);

    // THEN
    $exercise_db = Exercise::query()
        ->where('id', $exercise->getId()->getValue())
        ->first();

    expect($exercise_db)->not->toBeNull();
    $properties = json_decode($exercise_db->properties, true);

    for ($i = 0; $i < 3; ++$i) {
        expect($properties['sentences'][$i]['word'])->toEqual("word{$i}");
        expect($properties['sentences'][$i]['translation'])->toEqual("translation{$i}");
        expect($properties['sentences'][$i]['sentence'])->toEqual("sentence{$i}");
    }
});
