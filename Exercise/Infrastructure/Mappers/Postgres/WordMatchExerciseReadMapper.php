<?php

namespace Exercise\Infrastructure\Mappers\Postgres;

use Exercise\Application\DTO\WordMatchExerciseRead;
use Exercise\Application\DTO\WordMatchExerciseReadEntry;
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

        $entries = DB::table('exercises')
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

        $exercise_entries = [];
        foreach ($entries as $entry) {
            $properties = json_decode($entry->properties, true);

            $exercise_entries[] = new WordMatchExerciseReadEntry(
                exercise_entry_id: new ExerciseEntryId($entry->exercise_entry_id),
                word: $properties['sentences'][$entry->order]['word'] ?? '',
                word_translation: $properties['sentences'][$entry->order]['translation'] ?? '',
                sentence: $properties['sentences'][$entry->order]['sentence'] ?? '',
            );
        }

        $properties = json_decode($entries[0]->properties, true);

        return new WordMatchExerciseRead(
            exercise_id: new ExerciseId($entries[0]->id),
            is_story: $properties['story_id'] !== null,
            entries: $exercise_entries
        );
    }
}