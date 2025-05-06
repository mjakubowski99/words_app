<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Enum\SessionStatus;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Models\ActiveSessionFlashcard;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Models\ActiveSessionFlashcards;

class ActiveSessionFlashcardsMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function findBySessionFlashcardIds(array $session_flashcard_ids): ActiveSessionFlashcards
    {
        $results = $this->db::table('learning_session_flashcards')
            ->whereIn('learning_session_flashcards.id', $session_flashcard_ids)
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
            $data[] = new ActiveSessionFlashcard(
                new SessionId($result->id),
                SessionStatus::from($result->status),
                new UserId($result->user_id),
                $result->cards_per_session,
                new SessionFlashcardId($result->session_flashcard_id),
                new FlashcardId($result->flashcard_id),
                $result->rated_count,
                $result->rating !== null ? Rating::from($result->rating) : null,
                $result->flashcard_deck_id !== null,
            );
        }

        return new ActiveSessionFlashcards($data);
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

    public function save(ActiveSessionFlashcards $flashcards): void
    {
        if (count($flashcards->all()) === 0) {
            return;
        }

        $ids = [];
        $rating_statement = 'CASE ';
        foreach ($flashcards->all() as $flashcard) {
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
    }
}
