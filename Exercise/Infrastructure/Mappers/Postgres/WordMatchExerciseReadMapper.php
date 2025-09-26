<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Mappers\Postgres;

use Exercise\Application\DTO\Exercise\WordMatchExerciseRead;
use Exercise\Application\DTO\Exercise\WordMatchExerciseReadEntry;
use Exercise\Infrastructure\Models\WordMatchExerciseJsonProperties;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Utils\ValueObjects\ExerciseId;

class WordMatchExerciseReadMapper
{
    public function findByEntryId(ExerciseEntryId $id): WordMatchExerciseRead
    {
        $exercise_id = DB::table('exercise_entries')
            ->where('id', $id->getValue())
            ->select('exercise_id')
            ->firstOrFail()
            ->exercise_id;

        $entries = $this->getEntries($exercise_id);

        $properties = new WordMatchExerciseJsonProperties(json_decode($entries[0]->properties, true));

        $exercise_entries = [];
        foreach ($entries as $entry) {
            $exercise_entries[] = new WordMatchExerciseReadEntry(
                exercise_entry_id: new ExerciseEntryId($entry->exercise_entry_id),
                answered: $entry->last_answer !== null,
                word: $properties->getWord($entry->order),
                word_translation: $properties->getTranslation($entry->order),
                sentence: $properties->getSentence($entry->order),
            );
        }

        return new WordMatchExerciseRead(
            exercise_id: new ExerciseId($entries[0]->id),
            is_story: $properties->getStoryId() !== null,
            entries: $exercise_entries,
            options: $properties->getAnswerOptions(),
        );
    }

    private function getEntries(int $exercise_id): Collection
    {
        return DB::table('exercises')
            ->where('exercises.id', $exercise_id)
            ->join('exercise_entries', 'exercises.id', '=', 'exercise_entries.exercise_id')
            ->select(
                'exercises.id',
                'exercises.properties',
                'exercises.user_id',
                'exercises.status',
                'exercises.exercise_type',
                'exercises.properties',
                'exercise_entries.id as exercise_entry_id',
                'exercise_entries.last_answer',
                'exercise_entries.last_answer_correct',
                'exercise_entries.score',
                'exercise_entries.answers_count',
                'exercise_entries.order'
            )->get();
    }
}
