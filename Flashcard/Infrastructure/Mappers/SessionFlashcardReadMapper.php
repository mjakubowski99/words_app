<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Application\ReadModels\SessionFlashcardsRead;
use Illuminate\Support\Facades\DB;
use Shared\Enum\SessionStatus;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Application\ReadModels\SessionFlashcardRead;

class SessionFlashcardReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function findUnratedById(SessionId $session_id, int $limit): SessionFlashcardsRead
    {
        $stmt = "
            WITH session_data AS (
                SELECT 
                    id, 
                    cards_per_session,
                    status,
                    user_id 
                FROM 
                    learning_sessions
                WHERE 
                    id = ?
            ),
            flashcards_data AS ( 
                SELECT 
                    learning_session_flashcards.id, 
                    learning_session_flashcards.rating,
                    flashcards.word,
                    flashcards.word_lang,
                    flashcards.translation,
                    flashcards.translation_lang,
                    flashcards.context,
                    flashcards.context_translation
                FROM 
                    learning_session_flashcards
                LEFT JOIN
                    flashcards ON flashcards.id = learning_session_flashcards.flashcard_id
                WHERE 
                    learning_session_flashcards.learning_session_id = ?
                    AND learning_session_flashcards.rating IS NULL
                LIMIT ?
            ),
            rated_count AS (
                SELECT 
                    COUNT(id) AS count 
                FROM 
                    learning_session_flashcards 
                WHERE 
                    learning_session_id = ?
                    AND rating IS NOT NULL
            )
            SELECT 
                flashcards_data.id, 
                flashcards_data.rating,
                flashcards_data.word,
                flashcards_data.word_lang,
                flashcards_data.translation,
                flashcards_data.translation_lang,
                flashcards_data.context,
                flashcards_data.context_translation,
                session_data.id AS session_id,
                session_data.status as status,
                session_data.cards_per_session,
                session_data.user_id,
                rated_count.count AS rated_count
            FROM 
                session_data
            LEFT JOIN flashcards_data on true
            LEFT JOIN rated_count on true;
        ";

        $results = $this->db::select($stmt, [
            $session_id->getValue(),
            $session_id->getValue(),
            $limit,
            $session_id->getValue()
        ]);

        $session_flashcards = array_filter($results, function (object $result) {
            return $result->id !== null;
        });

        $session_flashcards = array_map(function (object $result) {
            return $this->map($result);
        }, $session_flashcards);

        return new SessionFlashcardsRead(
            $session_id,
            $results[0]->rated_count,
            $results[0]->cards_per_session,
            SessionStatus::from($results[0]->status) === SessionStatus::FINISHED,
            $session_flashcards
        );
    }

    private function map(object $data): SessionFlashcardRead
    {
        return new SessionFlashcardRead(
            new SessionFlashcardId($data->id),
            $data->word,
            Language::from($data->word_lang),
            $data->translation,
            Language::from($data->translation_lang),
            $data->context,
            $data->context_translation
        );
    }
}
