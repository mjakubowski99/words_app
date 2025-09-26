<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Mappers\Postgres;

use Exercise\Application\DTO\Exercise\UnscrambleWordExerciseRead;
use Illuminate\Support\Facades\DB;
use Shared\Models\Emoji;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Utils\ValueObjects\ExerciseId;

class UnscrambleWordExerciseReadMapper
{
    public function __construct(
        private DB $db,
    ) {}

    public function findByEntryId(ExerciseEntryId $id): UnscrambleWordExerciseRead
    {
        $data = $this->db::table('unscramble_word_exercises')
            ->where('exercise_entries.id', $id->getValue())
            ->join('exercise_entries', 'exercise_entries.exercise_id', '=', 'unscramble_word_exercises.exercise_id')
            ->select([
                'unscramble_word_exercises.exercise_id',
                'unscramble_word_exercises.scrambled_word',
                'unscramble_word_exercises.word',
                'unscramble_word_exercises.context_sentence',
                'unscramble_word_exercises.word_translation',
                'unscramble_word_exercises.context_sentence_translation',
                'unscramble_word_exercises.emoji',
                'exercise_entries.id as exercise_entry_id',
            ])->first();

        return new UnscrambleWordExerciseRead(
            new ExerciseId($data->exercise_id),
            $data->scrambled_word,
            $data->word_translation,
            $data->context_sentence,
            $data->context_sentence_translation,
            $data->word,
            $data->emoji ? Emoji::fromUnicode($data->emoji) : null,
            $data->exercise_entry_id,
        );
    }
}
