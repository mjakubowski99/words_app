<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Flashcard\Domain\Models\ActiveSession;
use Shared\Enum\SessionStatus;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Models\ActiveSessionFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;

class ActiveSessionFlashcardsMapper
{
    public function __construct(
        private DB $db,
    ) {}

    public function find(SessionId $id): ActiveSession
    {
        $results = $this->db::table('learning_session_flashcards')
            ->leftJoin('learning_sessions', 'learning_session_flashcards.learning_session_id', '=', 'learning_sessions.id')
            ->where('learning_sessions.id', $id)
            ->select([
                'learning_sessions.id',
                'learning_sessions.status',
                'learning_sessions.user_id',
                'learning_sessions.cards_per_session',
                'learning_sessions.flashcard_deck_id',
                'learning_sessions.user_id',
                'learning_sessions.status',
                'learning_session_flashcards.flashcard_id',
                'learning_session_flashcards.id as session_flashcard_id',
                'learning_session_flashcards.rating',
                'learning_session_flashcards.is_additional',
                'learning_session_flashcards.exercise_entry_id',
                DB::raw('
                    (SELECT COUNT(id) 
                    FROM learning_session_flashcards 
                    WHERE learning_session_id = learning_sessions.id 
                    AND rating IS NOT NULL 
                    AND is_additional = false) as rated_count
                '),
            ])->get();

        $session = null;
        foreach ($results as $result) {
            if ($session === null) {
                $session = new ActiveSession(
                    new SessionId($result->id),
                    new UserId($result->user_id),
                    $result->cards_per_session,
                    $result->rated_count,
                    $result->flashcard_deck_id !== null,
                );
            }

            $session->addSessionFlashcard(
                new ActiveSessionFlashcard(
                    new SessionFlashcardId($result->session_flashcard_id),
                    new FlashcardId($result->flashcard_id),
                    $result->rating !== null ? Rating::from($result->rating) : null,
                    $result->exercise_entry_id,
                    $result->is_additional,
                )
            );
        }

        return $session;
    }

    /** @return ActiveSession[] */
    public function findByExerciseEntryIds(array $exercise_entry_ids): array
    {
        $results = $this->db::table('learning_session_flashcards')
            ->whereIn('learning_session_flashcards.exercise_entry_id', $exercise_entry_ids)
            ->whereIn('learning_sessions.status', SessionStatus::activeStatuses())
            ->leftJoin('learning_sessions', 'learning_session_flashcards.learning_session_id', '=', 'learning_sessions.id')
            ->select([
                'learning_sessions.id',
                'learning_sessions.status',
                'learning_sessions.user_id',
                'learning_sessions.cards_per_session',
                'learning_sessions.flashcard_deck_id',
                'learning_sessions.user_id',
                'learning_sessions.status',
                'learning_session_flashcards.flashcard_id',
                'learning_session_flashcards.id as session_flashcard_id',
                'learning_session_flashcards.rating',
                'learning_session_flashcards.is_additional',
                'learning_session_flashcards.exercise_entry_id',
                DB::raw('
                    (SELECT COUNT(id) 
                    FROM learning_session_flashcards 
                    WHERE learning_session_id = learning_sessions.id 
                    AND rating IS NOT NULL 
                    AND is_additional = false) as rated_count
                '),
            ])->get();

        $data = [];
        foreach ($results as $result) {
            if (!array_key_exists($result->id, $data)) {
                $data[$result->id] = new ActiveSession(
                    new SessionId($result->id),
                    new UserId($result->user_id),
                    $result->cards_per_session,
                    $result->rated_count,
                    $result->flashcard_deck_id !== null,
                );
            }

            $data[$result->id]->addSessionFlashcard(
                new ActiveSessionFlashcard(
                    new SessionFlashcardId($result->session_flashcard_id),
                    new FlashcardId($result->flashcard_id),
                    $result->rating !== null ? Rating::from($result->rating) : null,
                    $result->exercise_entry_id,
                    $result->is_additional,
                )
            );
        }

        return array_values($data);
    }

    public function findLatestRatings(array $session_flashcard_id): array
    {
        $flashcard_ids = $this->db::table('learning_session_flashcards')
            ->whereIn('id', $session_flashcard_id)
            ->select('flashcard_id')
            ->pluck('flashcard_id');

        $results = $this->db::table('learning_session_flashcards as lsf')
            ->joinSub(
                $this->db::table('learning_session_flashcards')
                    ->whereNotNull('rating')
                    ->whereIn('flashcard_id', $flashcard_ids)
                    ->select('flashcard_id', $this->db::raw('MAX(updated_at) as latest_updated_at'))
                    ->groupBy('flashcard_id'),
                'latest',
                function ($join) {
                    $join->on('lsf.flashcard_id', '=', 'latest.flashcard_id')
                        ->on('lsf.updated_at', '=', 'latest.latest_updated_at');
                }
            )
            ->whereNotNull('lsf.rating')
            ->select('lsf.flashcard_id', 'lsf.rating')
            ->get();

        $final = [];
        foreach ($results as $result) {
            $final[$result->flashcard_id] = $result->rating !== null ? Rating::from($result->rating) : null;
        }

        return $final;
    }

    public function save(ActiveSession $session): void
    {
        if (count($session->getSessionFlashcards()) === 0) {
            return;
        }

        $ids = [];
        $rating_statement = 'CASE ';
        foreach ($session->getSessionFlashcards() as $flashcard) {
            if ($flashcard->getRating() !== null) {
                $rating_statement .= "WHEN id = {$flashcard->getSessionFlashcardId()->getValue()} THEN {$flashcard->getRating()->value}\n";
                $ids[] = $flashcard->getSessionFlashcardId()->getValue();
            }
        }
        $rating_statement .= ' END';

        $this->db::table('learning_session_flashcards')
            ->whereIn('id', $ids)
            ->update([
                'rating' => DB::raw($rating_statement),
                'updated_at' => now(),
            ]);

        if ($session->isFinished()) {
            $this->db::table('learning_sessions')
                ->where('id', $session->getSessionId())
                ->update([
                    'status' => SessionStatus::FINISHED,
                    'updated_at' => now(),
                ]);
        }
    }
}
