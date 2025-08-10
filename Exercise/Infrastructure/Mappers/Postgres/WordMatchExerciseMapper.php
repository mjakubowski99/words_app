<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Mappers\Postgres;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\WordMatchAnswer;
use Exercise\Domain\Models\WordMatchExercise;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\WordMatchExerciseEntry;
use Exercise\Infrastructure\Models\WordMatchExerciseJsonProperties;

class WordMatchExerciseMapper
{
    public function __construct(
        private DB $db,
        private ExerciseEntryMapper $exercise_entry_mapper,
    ) {}

    public function find(ExerciseId $id): WordMatchExercise
    {
        $rows = $this->getExerciseWithEntries($id);

        if ($rows->isEmpty()) {
            throw new \Exception('No Word Match Exercise found for entry ID: ' . $id->getValue());
        }

        $properties = new WordMatchExerciseJsonProperties(json_decode($rows[0]->properties, true));

        $entries = [];
        foreach ($rows as $row) {
            $entries[] = $this->mapEntry($properties, $row);
        }

        return $this->mapExercise($properties, $rows[0], $entries);
    }

    public function create(WordMatchExercise $exercise): ExerciseId
    {
        $exercise_id = DB::table('exercises')
            ->insertGetId([
                'exercise_type' => $exercise->getExerciseType()->toNumber(),
                'user_id' => $exercise->getUserId(),
                'status' => $exercise->getStatus()->value,
                'properties' => json_encode(WordMatchExerciseJsonProperties::fromExercise($exercise)->toJsonArray()),
            ]);

        $this->exercise_entry_mapper->insert($exercise_id, $exercise->getExerciseEntries());

        return new ExerciseId($exercise_id);
    }

    public function save(WordMatchExercise $exercise): void
    {
        $this->db::table('exercises')
            ->where('id', $exercise->getId()->getValue())
            ->update([
                'exercise_type' => $exercise->getExerciseType()->toNumber(),
                'user_id' => $exercise->getUserId(),
                'status' => $exercise->getStatus()->value,
                'properties' => json_encode(WordMatchExerciseJsonProperties::fromExercise($exercise)->toJsonArray()),
            ]);

        $this->exercise_entry_mapper->save($exercise->getExerciseEntries());
    }

    private function getExerciseWithEntries(ExerciseId $id): Collection
    {
        return $this->db::table('exercises')
            ->where('exercises.id', $id->getValue())
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
    }

    private function mapEntry(WordMatchExerciseJsonProperties $properties, object $row)
    {
        $entry_id = new ExerciseEntryId($row->exercise_entry_id);

        return new WordMatchExerciseEntry(
            $properties->getWord($row->order),
            $properties->getTranslation($row->order),
            $properties->getSentence($row->order),
            $entry_id,
            new ExerciseId($row->exercise_id),
            new WordMatchAnswer($entry_id, $row->correct_answer),
            $row->last_answer ? new WordMatchAnswer($entry_id, $row->last_answer) : null,
            $row->last_answer_correct,
            $row->order,
            (float) $row->score,
            (int) $row->answers_count
        );
    }

    /** @param WordMatchExerciseEntry[] $entries */
    private function mapExercise(WordMatchExerciseJsonProperties $properties, object $row, array $entries): WordMatchExercise
    {
        return new WordMatchExercise(
            $properties->getStoryId(),
            new ExerciseId($row->exercise_id),
            new UserId($row->user_id),
            ExerciseStatus::from($row->status),
            $entries,
            $properties->getAnswerOptions()
        );
    }
}
