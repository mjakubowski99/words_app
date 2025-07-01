<?php

namespace Tests\Unit\Exercise\Infrastructure\Repository\WordMatchExerciseReadRepository;

use Exercise\Infrastructure\Repositories\WordMatchExerciseReadRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Utils\ValueObjects\StoryId;
use Tests\TestCase;

class WordMatchExerciseReadRepositoryTest extends TestCase
{
    use WordMatchExerciseReadRepositoryTrait;
    use DatabaseTransactions;

    private WordMatchExerciseReadRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = app(WordMatchExerciseReadRepository::class);
    }

    public function test__findByEntryId_existingEntry_returnsExerciseWithEntries(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = $this->createWordMatchExercise($user->getId());
        $entry_id = $exercise->getExerciseEntries()[0]->getId();

        // WHEN
        $found_exercise = $this->repository->findByEntryId($entry_id);

        // THEN
        $this->assertEquals($exercise->getId(), $found_exercise->getExerciseId());
        $this->assertFalse($found_exercise->isStory());

        $entry = $exercise->getExerciseEntries()[0];
        $found_entry = $found_exercise->getEntries()[0];

        $this->assertEquals($entry->getId(), $found_entry->getExerciseEntryId());
        $this->assertEquals($entry->getWord(), $found_entry->getWord());
        $this->assertEquals($entry->getWordTranslation(), $found_entry->getWordTranslation());
        $this->assertEquals($entry->getSentence(), $found_entry->getSentence());
    }

    public function test__findByEntryId_exerciseWithMultipleEntries_returnsAllEntries(): void
    {
        // GIVEN
        $user = $this->createUser();
        $exercise = $this->createWordMatchExerciseWithMultipleEntries($user->getId());
        $entry_id = $exercise->getExerciseEntries()[0]->getId();

        // WHEN
        $found_exercise = $this->repository->findByEntryId($entry_id);

        // THEN
        $this->assertEquals($exercise->getId(), $found_exercise->getExerciseId());
        $this->assertCount(3, $found_exercise->getEntries());

        foreach ($exercise->getExerciseEntries() as $i => $entry) {
            $found_entry = $found_exercise->getEntries()[$i];

            $this->assertEquals($entry->getId(), $found_entry->getExerciseEntryId());
            $this->assertEquals($entry->getWord(), $found_entry->getWord());
            $this->assertEquals($entry->getWordTranslation(), $found_entry->getWordTranslation());
            $this->assertEquals($entry->getSentence(), $found_entry->getSentence());
        }
    }

    public function test__findByEntryId_exerciseWithStoryId_returnsExerciseWithStoryFlag(): void
    {
        // GIVEN
        $user = $this->createUser();
        $story = $this->createStory();
        $exercise = $this->createWordMatchExerciseWithStory(new StoryId($story->id), $user->getId());
        $entry_id = $exercise->getExerciseEntries()[0]->getId();

        // WHEN
        $found_exercise = $this->repository->findByEntryId($entry_id);

        // THEN
        $this->assertTrue($found_exercise->isStory());
    }

    public function test__findByEntryId_nonExistentEntry_throwsException(): void
    {
        // GIVEN
        $non_existent_id = new ExerciseEntryId(10000);

        // THEN
        $this->expectException(\Exception::class);

        // WHEN
        $this->repository->findByEntryId($non_existent_id);
    }

}
