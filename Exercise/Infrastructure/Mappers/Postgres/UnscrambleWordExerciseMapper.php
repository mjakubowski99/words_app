<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Mappers\Postgres;

use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Exercise\Domain\Models\ExerciseEntry;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\UnscrambleWordAnswer;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\SessionFlashcardId;

class UnscrambleWordExerciseMapper
{
    public function __construct(
        private DB $db,
    ) {}

    public function find(ExerciseId $id): UnscrambleWordsExercise
    {
        $result = $this->db::table('exercises')
            ->where('exercises.id', $id->getValue())
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
                'exercise_entries.session_flashcard_id',
                'exercise_entries.last_answer',
                'exercise_entries.last_answer_correct',
            )
            ->firstOrFail();

        $entry_id = new ExerciseEntryId($result->exercise_entry_id);

        return new UnscrambleWordsExercise(
            new ExerciseId($result->id),
            new UserId($result->user_id),
            ExerciseStatus::from($result->status),
            $entry_id,
            $result->word,
            $result->context_sentence,
            $result->word_translation,
            $result->emoji,
            $result->scrambled_word,
            $result->last_answer ? new UnscrambleWordAnswer($entry_id, $result->last_answer) : null,
            $result->last_answer_correct
        );
    }

    public function findByEntryId(ExerciseEntryId $id): UnscrambleWordsExercise
    {
        $result = $this->db::table('exercises')
            ->where('exercise_entries.id', $id->getValue())
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
                'exercise_entries.session_flashcard_id',
                'exercise_entries.last_answer',
                'exercise_entries.last_answer_correct',
            )
            ->firstOrFail();

        $entry_id = new ExerciseEntryId($result->exercise_entry_id);

        return new UnscrambleWordsExercise(
            new ExerciseId($result->id),
            new UserId($result->user_id),
            ExerciseStatus::from($result->status),
            $entry_id,
            $result->word,
            $result->context_sentence,
            $result->word_translation,
            $result->emoji,
            $result->scrambled_word,
            $result->last_answer ? new UnscrambleWordAnswer($entry_id, $result->last_answer) : null,
            $result->last_answer_correct
        );
    }

    public function create(UnscrambleWordsExercise $exercise): ExerciseId
    {
        if (!$exercise->getId()->isEmpty()) {
            throw new \InvalidArgumentException('Cannot create exercise with already existing id');
        }

        $exercise_id = $this->db::table('exercises')
            ->insertGetId([
                'exercise_type' => $exercise->getExerciseType()->value,
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
                'emoji' => $exercise->getEmoji(),
            ]);

        $data = [];

        /** @var ExerciseEntry $entry */
        foreach ($exercise->getExerciseEntries() as $entry) {
            $data[] = [
                'exercise_id' => $exercise_id,
                'correct_answer' => $entry->getCorrectAnswer()->toString(),
                'score' => 0.0,
                'answers_count' => 0,
                'last_answer' => null,
                'last_answer_correct' => null,
            ];
        }

        $this->db::table('exercise_entries')->insert($data);

        return new ExerciseId($exercise_id);
    }

    public function save(UnscrambleWordsExercise $exercise): void
    {
        $this->db::table('exercises')
            ->where('id', $exercise->getId())
            ->update([
                'exercise_type' => $exercise->getExerciseType()->value,
                'status' => $exercise->getStatus()->value,
            ]);

        $data = [];

        /** @var ExerciseEntry $entry */
        foreach ($exercise->getUpdatedEntries() as $entry) {
            $data[$entry->getId()->getValue()] = [
                'exercise_id' => $exercise->getId(),
                'correct_answer' => $entry->getCorrectAnswer()->toString(),
                'score' => $entry->getScore(),
                'answers_count' => $entry->getAnswersCount(),
                'last_answer' => $entry->getLastUserAnswer() ? $entry->getLastUserAnswer()->toString() : null,
                'last_answer_correct' => $entry->isLastAnswerCorrect(),
            ];
        }

        foreach ($data as $id => $update_data) {
            $this->db::table('exercise_entries')->where('id', $id)->update($update_data);
        }
    }
}
