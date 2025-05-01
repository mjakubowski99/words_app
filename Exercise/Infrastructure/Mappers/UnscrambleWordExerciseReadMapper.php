<?php

namespace Exercise\Infrastructure\Mappers;

use Exercise\Application\ReadModels\UnscrambleWordExerciseRead;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\ExerciseId;

class UnscrambleWordExerciseReadMapper
{
    public function __construct(
        private DB $db,
    ) {}

    public function find(ExerciseId $id): UnscrambleWordExerciseRead
    {
        $data = $this->db::table('unscramble_word_exercises')
            ->where('exercise_entries.exercise_id', $id->getValue())
            ->join('exercise_entries', 'exercise_entries.exercise_id', '=', 'unscramble_word_exercises.exercise_id')
            ->select([
                'unscramble_word_exercises.exercise_id',
                'unscramble_word_exercises.scrambled_word',
                'unscramble_word_exercises.word',
                'unscramble_word_exercises.context_sentence',
                'unscramble_word_exercises.word_translation',
                'unscramble_word_exercises.emoji',
                'exercise_entries.id as exercise_entry_id',
            ])->first();

        return new UnscrambleWordExerciseRead(
            new ExerciseId($data->exercise_id),
            $data->scrambled_word,
            $data->word_translation,
            $data->context_sentence,
            $data->emoji,
            $data->exercise_entry_id,
        );
    }
}