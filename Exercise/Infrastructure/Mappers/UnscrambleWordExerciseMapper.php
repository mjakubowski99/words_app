<?php

namespace Exercise\Infrastructure\Mappers;

use Exercise\Domain\Models\AnswerEntry;
use Exercise\Domain\Models\ExerciseStatus;
use Exercise\Domain\Models\UnscrambleWordAnswer;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\AnswerEntryId;
use Exercise\Domain\ValueObjects\ExerciseId;
use Exercise\Domain\ValueObjects\SessionFlashcardId;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;

class UnscrambleWordExerciseMapper
{
    public function __construct(
        private DB $db,
    ) {}

    public function find(ExerciseId $id): UnscrambleWordsExercise
    {
        $entries = $this->db::table('exercises')
            ->where('id', $id->getValue())
            ->leftJoin('answer_entries', 'answer_entries.exercise_id', '=', 'exercises.id')
            ->join('unscramble_words_exercise', 'exercises.id', '=', 'unscramble_words_exercise.exercise_id')
            ->select('exercises.*', 'unscramble_words_exercise.*', 'answer_entries.*')
            ->get();

        $result_entries = [];
        foreach ($entries as $db) {
            if ($db->answer_entry_id === null) {
                continue;
            }
            $entry_id = new AnswerEntryId($db->answer_entry_id);
            $entry = new AnswerEntry(
                $entry_id,
                new ExerciseId($db->exercise_id),
                UnscrambleWordAnswer::fromString($entry_id, $db->correct_answer),
                $db->last_answer ? UnscrambleWordAnswer::fromString($entry_id, $db->last_answer) : null,
                $db->last_answer_correct,
                new SessionFlashcardId($db->session_flashcard_id),
            );
        }

        return new UnscrambleWordsExercise(
            new ExerciseId($db->exercise_id),
            new UserId($db->user_id),
            ExerciseStatus::from($db->status),
            $result_entries[0]->getId(),
            $result_entries[0]->getSessionFlashcardId(),
            $db->context_sentence,
            $db->word,
            $db->word_translation,
            $db->scrambled_word,
            $db->emoji
        );
    }

    public function create(UnscrambleWordsExercise $exercise): ExerciseId
    {
        $exercise_id = $this->db::table('exercises')
            ->insertGetId([
                'type' => $exercise->getExerciseType()->value,
                'user_id' => $exercise->getUserId(),
                'status' => $exercise->getStatus()->value,
            ]);

        $this->db::table('unscramble_words_exercise')
            ->insert([
                'exercise_id' => $exercise_id,
                'word' => $exercise->getWord(),
                'context_sentence' => $exercise->getContextSentence(),
                'word_translation' => $exercise->getWordTranslation(),
                'scrambled_word' => $exercise->getScrambledWord(),
                'emoji' => $exercise->getEmoji(),
            ]);

        $data = [];

        /** @var AnswerEntry $entry */
        foreach ($exercise->getAnswerEntries() as $entry) {
            $data[] = [
                'exercise_id' => $exercise_id,
                'answer_id' => $entry->getCorrectAnswer()->toString(),
                'score' => null,
                'last_answer' => null,
                'last_answer_correct' => null,
                'session_flashcard_id' => $entry->getSessionFlashcardId(),
            ];
        }

        $this->db::table('answer_entries')
            ->insert($data);

        return new ExerciseId($exercise_id);
    }
}