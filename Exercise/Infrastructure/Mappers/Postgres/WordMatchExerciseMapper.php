<?php

namespace Exercise\Infrastructure\Mappers\Postgres;

use Exercise\Domain\Models\ExerciseEntry;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\WordMatchAnswer;
use Exercise\Domain\Models\WordMatchExercise;
use Exercise\Domain\Models\WordMatchExerciseEntry;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\StoryId;
use Shared\Utils\ValueObjects\UserId;
use Spatie\Regex\Helpers\Arr;

class WordMatchExerciseMapper
{
    public function __construct(
        private DB $db,
    ) {}

    public function find(ExerciseId $id): WordMatchExercise
    {
        $rows = DB::table('exercises')
            ->where('exercises.id', $id)
            ->join('exercise_entries', 'exercise_entries.exercise_id', '=', 'exercises.id')
            ->select(
                'exercises.properties',
                'exercises.id as exercise_id',
                'exercises.user_id as user_id',
                'exercises.status',
                'exercise_entries.id as exercise_entry_id',
                'exercise_entries.last_answer',
                'exercise_entries.last_answer_correct',
                'exercise_entries.score',
                'exercise_entries.answers_count',
                'exercise_entries.correct_answer',
                'exercise_entries.order',
            )->get();

        if ($rows->isEmpty()) {
            throw new \Exception("No Word Match Exercise found for entry ID: " . $id->getValue());
        }

        $entries = [];
        foreach ($rows as $row) {
            $entry_id = new ExerciseEntryId($row->exercise_entry_id);
            $properties = json_decode($row->properties, true);

            $entries[] = new WordMatchExerciseEntry(
                $properties['sentences'][$row->order]['word'] ?? '',
                    $properties['sentences'][$row->order]['translation'] ?? '',
                    $properties['sentences'][$row->order]['sentence'] ?? '',
                $entry_id,
                new ExerciseId($row->exercise_id),
                new WordMatchAnswer($entry_id, $row->correct_answer),
                $row->last_answer ? new WordMatchAnswer($entry_id, $row->last_answer) : null,
                $row->last_answer_correct,
                (float) $row->score,
                (int) $row->answers_count
            );
        }

        $properties = json_decode($rows[0]->properties, true);

        return new WordMatchExercise(
            $properties['story_id'] ? new StoryId($properties['story_id']) : null,
            new ExerciseId($rows[0]->exercise_id),
            new UserId($rows[0]->user_id),
            ExerciseStatus::from($rows[0]->status),
            $entries
        );
    }

    public function create(WordMatchExercise $exercise): void
    {
        $properties = [
            'story_id' => $exercise->getStoryId()?->getValue(),
            'sentences' => [],
        ];

        $i = 0;
        foreach ($exercise->getExerciseEntries() as $entry) {
            $properties['sentences'][] = [
                'order' => $i,
                'sentence' => $entry->getSentence(),
                'word' => $entry->getWord(),
                'translation' => $entry->getWordTranslation(),
            ];
            $i++;
        }

        $exercise_id = DB::table('exercises')
            ->insertGetId([
                'exercise_type' => $exercise->getExerciseType()->toNumber(),
                'user_id' => $exercise->getUserId(),
                'status' => $exercise->getStatus()->value,
                'properties' => json_encode($properties),
            ]);

        $i = 0;
        /** @var ExerciseEntry $entry */
        foreach ($exercise->getExerciseEntries() as $entry) {
            $data[] = [
                'exercise_id' => $exercise_id,
                'correct_answer' => $entry->getCorrectAnswer()->toString(),
                'score' => 0.0,
                'answers_count' => 0,
                'last_answer' => null,
                'last_answer_correct' => null,
                'order' => $i,
            ];
            $i++;
        }

        $this->db::table('exercise_entries')->insert($data);
    }

    public function save(WordMatchExercise $exercise): void
    {
        $properties = [
            'story_id' => $exercise->getStoryId()?->getValue(),
            'sentences' => [],
        ];

        $i = 0;
        foreach ($exercise->getExerciseEntries() as $entry) {
            $properties['sentences'][] = [
                'order' => $i,
                'sentence' => $entry->getSentence(),
                'word' => $entry->getWord(),
                'translation' => $entry->getWordTranslation(),
            ];
            $i++;
        }

        DB::table('exercises')
            ->where('id', $exercise->getId()->getValue())
            ->update([
                'exercise_type' => $exercise->getExerciseType()->toNumber(),
                'user_id' => $exercise->getUserId(),
                'status' => $exercise->getStatus()->value,
                'properties' => json_encode($properties),
            ]);

        $i = 0;
        /** @var ExerciseEntry $entry */
        foreach ($exercise->getExerciseEntries() as $entry) {
            $this->db::table('exercise_entries')
                ->where('id', $entry->getId()->getValue())
                ->update([
                    'correct_answer' => $entry->getCorrectAnswer()->toString(),
                    'score' => $entry->getScore(),
                    'answers_count' => $entry->getAnswersCount(),
                    'last_answer' => $entry->getLastUserAnswer()?->toString(),
                    'last_answer_correct' => $entry->isLastAnswerCorrect(),
                    'order' => $i,
                ]);
            $i++;
        }
    }
}