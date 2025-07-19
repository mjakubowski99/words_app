<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Mappers\Postgres;

use Shared\Models\Emoji;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Exercise\Domain\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\UnscrambleWordAnswer;
use Exercise\Domain\Models\UnscrambleWordsExercise;

class UnscrambleWordExerciseMapper
{
    public function __construct(
        private DB $db,
        private ExerciseEntryMapper $exercise_entry_mapper,
    ) {}

    public function find(ExerciseId $id): UnscrambleWordsExercise
    {
        $result = $this->getExerciseQuery($id, null);
        return $this->map($result);
    }

    public function findByEntryId(ExerciseEntryId $id): UnscrambleWordsExercise
    {
        $result = $this->getExerciseQuery(null, $id);
        return $this->map($result);
    }

    public function create(UnscrambleWordsExercise $exercise): ExerciseId
    {
        if (!$exercise->getId()->isEmpty()) {
            throw new \InvalidArgumentException('Cannot create exercise with already existing id');
        }

        $exercise_id = $this->db::table('exercises')->insertGetId([
            'exercise_type' => $exercise->getExerciseType()->toNumber(),
            'user_id' => $exercise->getUserId(),
            'status' => $exercise->getStatus()->value,
        ]);

        $this->db::table('unscramble_word_exercises')
            ->insert([
                'exercise_id' => $exercise_id,
                'word' => $exercise->getWord(),
                'context_sentence' => $exercise->getContextSentence(),
                'word_translation' => $exercise->getWordTranslation(),
                'scrambled_word' => $exercise->getScrambledWord(),
                'emoji' => $exercise->getEmoji()?->toUnicode(),
            ]);

        $this->exercise_entry_mapper->insert($exercise_id, $exercise->getExerciseEntries());

        return new ExerciseId($exercise_id);
    }

    public function save(UnscrambleWordsExercise $exercise): void
    {
        $this->db::table('exercises')
            ->where('id', $exercise->getId())
            ->update([
                'exercise_type' => $exercise->getExerciseType()->toNumber(),
                'status' => $exercise->getStatus()->value,
            ]);

        $this->exercise_entry_mapper->save($exercise->getUpdatedEntries());
    }

    private function getExerciseQuery(?ExerciseId $id, ?ExerciseEntryId $entry_id): object
    {
        return $this->db::table('exercises')
            ->when($id!==null, fn($q) => $q->where('exercises.id', $id->getValue()))
            ->when($entry_id!==null, fn($q) => $q->where('exercise_entries.id', $entry_id->getValue()))
            ->join('exercise_entries', 'exercise_entries.exercise_id', '=', 'exercises.id')
            ->join(
                'unscramble_word_exercises',
                'exercises.id',
                '=',
                'unscramble_word_exercises.exercise_id'
            )
            ->select(
                'exercises.id',
                'exercises.user_id',
                'exercises.status',
                'exercises.exercise_type',
                'unscramble_word_exercises.word',
                'unscramble_word_exercises.scrambled_word',
                'unscramble_word_exercises.context_sentence',
                'unscramble_word_exercises.word_translation',
                'unscramble_word_exercises.emoji',
                'exercise_entries.id as exercise_entry_id',
                'exercise_entries.last_answer',
                'exercise_entries.last_answer_correct',
                'exercise_entries.score',
                'exercise_entries.answers_count',
            )
            ->firstOrFail();
    }

    private function map(object $row): UnscrambleWordsExercise
    {
        $entry_id = new ExerciseEntryId($row->exercise_entry_id);

        return new UnscrambleWordsExercise(
            new ExerciseId($row->id),
            new UserId($row->user_id),
            ExerciseStatus::from($row->status),
            $entry_id,
            $row->word,
            $row->context_sentence,
            $row->word_translation,
            $row->emoji ? Emoji::fromUnicode($row->emoji) : null,
            $row->scrambled_word,
            $row->last_answer ? new UnscrambleWordAnswer($entry_id, $row->last_answer) : null,
            $row->last_answer_correct,
            (float) $row->score,
            $row->answers_count,
        );
    }
}
