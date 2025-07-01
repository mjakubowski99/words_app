<?php

declare(strict_types=1);

namespace Tests\Unit\Exercise\Infrastructure\Repository\WordMatchExerciseRepository;

use App\Models\Exercise;
use App\Models\ExerciseEntry;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Infrastructure\Repositories\WordMatchExerciseRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Utils\ValueObjects\StoryId;
use Tests\TestCase;

class WordMatchExerciseRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    use WordMatchExerciseRepositoryTrait;

    private WordMatchExerciseRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        ExerciseEntry::query()->forceDelete();
        Exercise::query()->forceDelete();
        $this->repository = $this->app->make(WordMatchExerciseRepository::class);
    }

    public function test__find_existingExercise_returnsExerciseWithEntries(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = $this->createWordMatchExercise($user->getId());
        $this->repository->create($exercise);

        // WHEN
        $found_exercise = $this->repository->find($exercise->getId());

        // THEN
        $this->assertEquals($exercise->getId(), $found_exercise->getId());
        $this->assertEquals($exercise->getUserId(), $found_exercise->getUserId());
        $this->assertEquals($exercise->getStatus(), $found_exercise->getStatus());

        $entry = $exercise->getExerciseEntries()[0];
        $found_entry = $found_exercise->getExerciseEntries()[0];

        $this->assertEquals($entry->getId(), $found_entry->getId());
        $this->assertEquals($entry->getWord(), $found_entry->getWord());
        $this->assertEquals($entry->getWordTranslation(), $found_entry->getWordTranslation());
        $this->assertEquals($entry->getSentence(), $found_entry->getSentence());
        $this->assertEquals($entry->getCorrectAnswer(), $found_entry->getCorrectAnswer());
    }

    public function test__find_exerciseWithMultipleEntries_returnsAllEntries(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = $this->createWordMatchExerciseWithMultipleEntries($user->getId());
        $this->repository->create($exercise);

        // WHEN
        $found_exercise = $this->repository->find($exercise->getId());

        // THEN
        $this->assertCount(3, $found_exercise->getExerciseEntries());

        foreach ($exercise->getExerciseEntries() as $i => $entry) {
            $found_entry = $found_exercise->getExerciseEntries()[$i];

            $this->assertEquals($entry->getId(), $found_entry->getId());
            $this->assertEquals($entry->getWord(), $found_entry->getWord());
            $this->assertEquals($entry->getWordTranslation(), $found_entry->getWordTranslation());
            $this->assertEquals($entry->getSentence(), $found_entry->getSentence());
            $this->assertEquals($entry->getCorrectAnswer(), $found_entry->getCorrectAnswer());
        }
    }


    public function test__create_validExercise_storesExerciseWithEntries(): void
    {
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
    }

    public function test__create_exerciseWithMultipleEntries_storesAllEntries(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = $this->createNewWordMatchExerciseWithMultipleEntries($user->getId());

        // WHEN
        $this->repository->create($exercise);

        // THEN
        $this->assertDatabaseCount('exercise_entries', 3);
    }

    public function test__create_exerciseWithMultipleEntries_storesCorrectProperties(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = $this->createNewWordMatchExerciseWithMultipleEntries($user->getId());

        // WHEN
        $this->repository->create($exercise);

        // THEN
        $exercise_db = Exercise::query()
            ->where('user_id', $exercise->getUserId()->getValue())
            ->first();

        $this->assertNotNull($exercise_db);
        $properties = json_decode($exercise_db->properties, true);

        $this->assertIsArray($properties['sentences']);
        $this->assertCount(3, $properties['sentences']);

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals($i, $properties['sentences'][$i]['order']);
            $this->assertEquals("word$i", $properties['sentences'][$i]['word']);
            $this->assertEquals("translation$i", $properties['sentences'][$i]['translation']);
            $this->assertEquals("sentence$i", $properties['sentences'][$i]['sentence']);
        }

        $entries = ExerciseEntry::query()
            ->where('exercise_id', $exercise_db->id)
            ->orderBy('order')
            ->get();

        $this->assertCount(3, $entries);
        foreach ($entries as $i => $entry) {
            $this->assertEquals($i, $entry->order);
            $this->assertEquals("correct$i", $entry->correct_answer);
        }
    }

    public function test__create_exerciseWithStoryId_storesStoryIdInProperties(): void
    {
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

        $this->assertNotNull($exercise_db);
        $properties = json_decode($exercise_db->properties, true);

        $this->assertArrayHasKey('story_id', $properties);
        $this->assertEquals($exercise->getStoryId()->getValue(), $properties['story_id']);
        $this->assertNotEmpty($properties['sentences']);
    }

    public function test__save_updatedExercise_updatesExerciseAndEntries(): void
    {
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
    }

    public function test__save_exerciseWithMultipleEntries_updatesAllEntries(): void
    {
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

        $this->assertCount(3, $entries);
        foreach ($entries as $entry) {
            $this->assertEquals(20.0, $entry->score);
            $this->assertEquals(2, $entry->answers_count);
        }
    }

    public function test__save_exerciseWithUpdatedProperties_updatesProperties(): void
    {
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

        $this->assertNotNull($exercise_db);
        $properties = json_decode($exercise_db->properties, true);

        for ($i = 0; $i < 3; $i++) {
            $this->assertEquals("word$i", $properties['sentences'][$i]['word']);
            $this->assertEquals("translation$i", $properties['sentences'][$i]['translation']);
            $this->assertEquals("sentence$i", $properties['sentences'][$i]['sentence']);
        }
    }
}
