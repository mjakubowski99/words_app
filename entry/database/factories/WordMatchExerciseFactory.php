<?php

declare(strict_types=1);

namespace Database\Factories;

use Faker\Factory;
use App\Models\Story;
use App\Models\Exercise;
use App\Models\ExerciseEntry;
use Shared\Enum\ExerciseType;

class WordMatchExerciseFactory extends ExerciseFactory
{
    public static function createNew(array $attributes, int $entries_count, bool $with_story): Exercise
    {
        $properties = [
            'story_id' => $with_story ? Story::factory()->create()->id : null,
            'sentences' => [],
        ];
        for ($i = 0; $i < $entries_count; ++$i) {
            $word = Factory::create()->word;
            $properties['sentences'][] = [
                'word' => $word,
                'sentence' => Factory::create()->word . $word . Factory::create()->word,
                'translation' => Factory::create()->word,
            ];
        }
        $exercise = Exercise::factory()->create(array_merge($attributes, [
            'properties' => json_encode($properties),
            'exercise_type' => ExerciseType::WORD_MATCH->toNumber(),
        ]));

        for ($i = 0; $i < $entries_count; ++$i) {
            ExerciseEntry::factory()->create([
                'exercise_id' => $exercise->id,
                'order' => $i,
                'last_answer' => null,
                'last_answer_correct' => null,
                'score' => 0.0,
                'answers_count' => 0,
            ]);
        }

        return $exercise;
    }
}
