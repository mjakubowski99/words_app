<?php

declare(strict_types=1);

namespace Tests\Unit\Exercise\Infrastructure\Repository\WordMatchExerciseRepository;

use App\Models\Story;
use App\Models\Exercise;
use App\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\Answer\WordMatchAnswer;
use Exercise\Domain\Models\Exercise\WordMatchExercise;
use Exercise\Domain\Models\ExerciseEntry\WordMatchExerciseEntry;

trait WordMatchExerciseRepositoryTrait
{
    private function createStory(array $attributes = []): Story
    {
        return Story::factory()->create($attributes);
    }

    private function createNewWordMatchExercise(UserId $user_id): WordMatchExercise
    {
        $entry_id = ExerciseEntryId::noId();
        $exercise_id = ExerciseId::noId();

        return new WordMatchExercise(
            null,
            ExerciseId::noId(),
            $user_id,
            ExerciseStatus::NEW,
            [
                new WordMatchExerciseEntry(
                    'word',
                    'translation',
                    'sentence',
                    $entry_id,
                    $exercise_id,
                    new WordMatchAnswer($entry_id, 'correct'),
                    null,
                    null,
                    0,
                    0.0,
                    0
                ),
            ],
            ['word', 'word2'],
        );
    }

    private function createWordMatchExercise(UserId $user_id): WordMatchExercise
    {
        $exercise = Exercise::factory()->create([
            'user_id' => $user_id->getValue(),
            'status' => ExerciseStatus::DONE->value,
            'properties' => json_encode([
                'story_id' => null,
                'sentences' => [
                    [
                        'order' => 0,
                        'word' => 'word',
                        'translation' => 'translation',
                        'sentence' => 'sentence',
                    ],
                ],
            ]),
        ]);
        $entry = ExerciseEntry::factory()->create([
            'exercise_id' => $exercise->id,
            'order' => 0,
            'correct_answer' => 'correct',
        ]);

        return new WordMatchExercise(
            null,
            new ExerciseId($exercise->id),
            $user_id,
            ExerciseStatus::from($exercise->status),
            [
                new WordMatchExerciseEntry(
                    'word',
                    'translation',
                    'sentence',
                    new ExerciseEntryId($entry->id),
                    new ExerciseId($exercise->id),
                    new WordMatchAnswer(new ExerciseEntryId($entry->id), $entry->correct_answer),
                    null,
                    null,
                    0,
                    20.0,
                    2
                ),
            ],
            ['word', 'word2'],
        );
    }

    private function createWordMatchExerciseWithMultipleEntries(UserId $user_id): WordMatchExercise
    {
        $exercise = Exercise::factory()->create([
            'user_id' => $user_id->getValue(),
            'properties' => json_encode([
                'story_id' => null,
                'sentences' => [
                    [
                        'order' => 0,
                        'word' => 'word0',
                        'translation' => 'translation0',
                        'sentence' => 'sentence0',
                    ],
                    [
                        'order' => 1,
                        'word' => 'word1',
                        'translation' => 'translation1',
                        'sentence' => 'sentence1',
                    ],
                    [
                        'order' => 2,
                        'word' => 'word2',
                        'translation' => 'translation2',
                        'sentence' => 'sentence2',
                    ],
                ],
            ]),
        ]);

        $entries = [];
        for ($i = 0; $i < 3; ++$i) {
            $entry = ExerciseEntry::factory()->create([
                'exercise_id' => $exercise->id,
                'order' => $i,
                'correct_answer' => "correct{$i}",
            ]);

            $entries[] = new WordMatchExerciseEntry(
                "word{$i}",
                "translation{$i}",
                "sentence{$i}",
                new ExerciseEntryId($entry->id),
                new ExerciseId($exercise->id),
                new WordMatchAnswer(new ExerciseEntryId($entry->id), "correct{$i}"),
                null,
                null,
                $i,
                20.0,
                2
            );
        }

        return new WordMatchExercise(
            null,
            new ExerciseId($exercise->id),
            $user_id,
            ExerciseStatus::DONE,
            $entries,
            ['word', 'word2'],
        );
    }

    private function createNewWordMatchExerciseWithMultipleEntries(UserId $user_id): WordMatchExercise
    {
        $exercise_id = ExerciseId::noId();
        $entries = [];

        for ($i = 0; $i < 3; ++$i) {
            $entry_id = ExerciseEntryId::noId();
            $entries[] = new WordMatchExerciseEntry(
                "word{$i}",
                "translation{$i}",
                "sentence{$i}",
                $entry_id,
                $exercise_id,
                new WordMatchAnswer($entry_id, "correct{$i}"),
                null,
                null,
                $i,
                0.0,
                0
            );
        }

        return new WordMatchExercise(
            null,
            $exercise_id,
            $user_id,
            ExerciseStatus::NEW,
            $entries,
            ['word', 'word2'],
        );
    }

    private function createWordMatchExerciseWithStory(StoryId $story_id, UserId $user_id): WordMatchExercise
    {
        $entry_id = ExerciseEntryId::noId();
        $exercise_id = ExerciseId::noId();

        return new WordMatchExercise(
            $story_id,
            $exercise_id,
            $user_id,
            ExerciseStatus::NEW,
            [
                new WordMatchExerciseEntry(
                    'word',
                    'translation',
                    'sentence',
                    $entry_id,
                    $exercise_id,
                    new WordMatchAnswer($entry_id, 'correct'),
                    null,
                    null,
                    0,
                    0.0,
                    0
                ),
            ],
            ['word', 'word2', 'word3'],
        );
    }
}
