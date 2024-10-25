<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\OwnerId;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Models\RateableSessionFlashcards;
use Shared\Enum\FlashcardOwnerType;
use Shared\Enum\SessionStatus;

class RateableSessionFlashcardsMapper
{
    public function __construct(
        private readonly DB $db
    ) {}

    public function find(SessionId $id): RateableSessionFlashcards
    {
        $stmt = "
            WITH flashcards_data AS (
                SELECT id, flashcard_id 
                FROM learning_session_flashcards
                WHERE rating IS NULL AND learning_session_id = ?
            ),
            session_data AS (
                SELECT id AS session_id, status, user_id, cards_per_session 
                FROM learning_sessions 
                WHERE id = ?
            ),
            rated_flashcard_count AS (
                SELECT COUNT(id) AS count 
                FROM learning_session_flashcards 
                WHERE rating IS NOT NULL AND learning_session_id = ?
            )
            SELECT 
                sd.session_id, 
                sd.status, 
                sd.user_id,
                sd.cards_per_session,
                rfc.count AS rated_flashcard_count,
                lsf.id,
                lsf.flashcard_id 
            FROM 
                session_data sd
            LEFT JOIN 
                rated_flashcard_count AS rfc ON true
            LEFT JOIN 
                flashcards_data AS lsf ON true;
        ";

        $results = $this->db::select($stmt, [
            $id->getValue(),
            $id->getValue(),
            $id->getValue()
        ]);

        $flashcards = array_filter($results, fn(object $result) => $result->id !== null);

        $flashcards = array_map(fn (object $result) => new RateableSessionFlashcard(
            new SessionFlashcardId($result->id),
            new FlashcardId($result->flashcard_id)
        ), $flashcards);

        return new RateableSessionFlashcards(
            $id,
            new Owner(new OwnerId($results[0]->user_id), FlashcardOwnerType::USER),
            SessionStatus::from($results[0]->status),
            $results[0]->rated_flashcard_count,
            $results[0]->cards_per_session,
            $flashcards,
        );
    }

    public function save(RateableSessionFlashcards $flashcards): void
    {
        $this->db::table('learning_sessions')
            ->where('id', $flashcards->getSessionId())
            ->where('status', '!=', $flashcards->getStatus()->value)
            ->update([
                'status' => $flashcards->getStatus()->value,
                'updated_at' => now(),
            ]);

        if (count($flashcards->getRateableSessionFlashcards()) === 0) {
            return;
        }

        $ids = [];
        $rating_statement = "CASE ";
        foreach ($flashcards->getRateableSessionFlashcards() as $flashcard) {
            if ($flashcard->rated()) {
                $rating_statement .= "WHEN id = {$flashcard->getId()->getValue()} THEN {$flashcard->getRating()->value}\n";
                $ids[] = $flashcard->getId()->getValue();
            }
        }
        $rating_statement .= " END";

        $this->db::table('learning_session_flashcards')
            ->where('learning_session_id', $flashcards->getSessionId())
            ->whereIn('id', $ids)
            ->update([
                'rating' => DB::raw($rating_statement),
                'updated_at' => now(),
            ]);
    }
}
