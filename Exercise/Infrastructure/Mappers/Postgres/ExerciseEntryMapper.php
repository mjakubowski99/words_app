<?php

namespace Exercise\Infrastructure\Mappers\Postgres;

use Exercise\Domain\Models\ExerciseEntry;
use Illuminate\Support\Facades\DB;

class ExerciseEntryMapper
{
    public function insert(int $exercise_id, array $entries): void
    {
        /** @var ExerciseEntry $entry */
        foreach ($entries as $entry) {
            $data[] = [
                'exercise_id' => $exercise_id,
                'correct_answer' => $entry->getCorrectAnswer()->toString(),
                'score' => 0.0,
                'answers_count' => 0,
                'last_answer' => null,
                'last_answer_correct' => null,
                'order' => $entry->getOrder(),
            ];
        }

        DB::table('exercise_entries')->insert($data);
    }

    public function save(array $entries): void
    {
        /** @var ExerciseEntry $entry */
        foreach ($entries as $entry) {
            DB::table('exercise_entries')
                ->where('id', $entry->getId()->getValue())
                ->update([
                    'correct_answer' => $entry->getCorrectAnswer()->toString(),
                    'score' => $entry->getScore(),
                    'answers_count' => $entry->getAnswersCount(),
                    'last_answer' => $entry->getLastUserAnswer()?->toString(),
                    'last_answer_correct' => $entry->isLastAnswerCorrect(),
                    'order' => $entry->getOrder(),
                ]);
        }
    }
}