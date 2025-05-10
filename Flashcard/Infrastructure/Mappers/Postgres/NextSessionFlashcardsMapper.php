<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Enum\SessionType;
use Shared\Enum\LanguageLevel;
use Flashcard\Domain\Models\Deck;
use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;
use Shared\Exceptions\NotFoundException;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Domain\ValueObjects\FlashcardDeckId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Models\NextSessionFlashcardResult;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;

class NextSessionFlashcardsMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly DB $db,
        private readonly SessionMapper $session_mapper,
    ) {}

    public function find(SessionId $id): NextSessionFlashcards
    {
        $session = $this->session_mapper->find($id);

        $stmt = '
                WITH session_data AS (
                    SELECT 
                        ls.id AS session_id,
                        ls.user_id,
                        ls.type,
                        ls.cards_per_session,
                        ls.status AS session_status,
                        fd.id AS deck_id,
                        fd.user_id AS deck_user_id,
                        fd.admin_id as deck_admin_id,
                        fd.name AS deck_name,
                        fd.tag AS deck_tag,
                        fd.default_language_level AS deck_default_language_level 
                    FROM 
                        learning_sessions AS ls
                    LEFT JOIN 
                        flashcard_decks AS fd ON fd.id = ls.flashcard_deck_id
                    WHERE 
                        ls.id = ?
                ),
                counts AS (
                    SELECT 
                        lsf.learning_session_id,
                        COUNT(lsf.id) AS all_count,
                        COUNT(CASE WHEN lsf.rating IS NULL THEN 1 END) AS unrated_count
                    FROM 
                        learning_session_flashcards AS lsf
                    WHERE 
                        lsf.learning_session_id = ?
                        AND lsf.is_additional = false
                    GROUP BY 
                        lsf.learning_session_id
                )
                SELECT 
                    sd.session_id,
                    sd.user_id,
                    sd.type,
                    sd.cards_per_session,
                    sd.session_status,
                    sd.deck_id,
                    sd.deck_user_id,
                    sd.deck_admin_id,
                    sd.deck_name,
                    sd.deck_tag,
                    sd.deck_default_language_level,
                    c.unrated_count,
                    c.all_count
                FROM 
                    session_data AS sd
                LEFT JOIN 
                    counts AS c ON c.learning_session_id = sd.session_id;
        ';

        $results = $this->db::select($stmt, [
            $session->getId(),
            $session->getId(),
        ]);

        if (count($results) === 0) {
            throw new NotFoundException("Session with id: {$id->getValue()} not found");
        }

        $result = $results[0];

        if ($result->deck_id) {
            $owner = $this->buildOwner((string) $result->deck_user_id, (string) $result->deck_admin_id);
            $deck = $this->mapDeck($owner, $result);
        } else {
            $deck = null;
        }

        return new NextSessionFlashcards(
            $id,
            SessionType::from($result->type),
            new UserId($result->user_id),
            $deck,
            $result->all_count ?? 0,
            $result->unrated_count ?? 0,
            $result->cards_per_session
        );
    }

    public function save(NextSessionFlashcards $next_session_flashcards): void
    {
        $insert_data = [];
        $now = now();

        foreach ($next_session_flashcards->getNextFlashcards() as $next_session_flashcard) {
            $insert_data[] = [
                'learning_session_id' => $next_session_flashcards->getSessionId()->getValue(),
                'flashcard_id' => $next_session_flashcard->getFlashcardId()->getValue(),
                'rating' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'is_additional' => false,
                'exercise_entry_id' => $next_session_flashcard->hasExercise() ? $next_session_flashcard->getExerciseEntryId() : null,
                'exercise_type' => $next_session_flashcard->hasExercise() ? $next_session_flashcard->getExerciseType()->toNumber() : null,
            ];
        }

        foreach ($next_session_flashcards->getAdditionalFlashcards() as $next_session_flashcard) {
            $insert_data[] = [
                'learning_session_id' => $next_session_flashcards->getSessionId()->getValue(),
                'flashcard_id' => $next_session_flashcard->getFlashcardId()->getValue(),
                'rating' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'is_additional' => true,
                'exercise_entry_id' => $next_session_flashcard->hasExercise() ? $next_session_flashcard->getExerciseEntryId() : null,
                'exercise_type' => $next_session_flashcard->hasExercise() ? $next_session_flashcard->getExerciseType()->toNumber(): null,
            ];
        }

        $this->db::table('learning_session_flashcards')
            ->insert($insert_data);
    }

    private function mapDeck(Owner $owner, object $result): ?Deck
    {
        $deck = $result->deck_id ? new Deck(
            $owner,
            $result->deck_tag,
            $result->deck_name,
            LanguageLevel::from($result->deck_default_language_level)
        ) : null;
        $deck?->init(new FlashcardDeckId($result->deck_id));

        return $deck;
    }
}
