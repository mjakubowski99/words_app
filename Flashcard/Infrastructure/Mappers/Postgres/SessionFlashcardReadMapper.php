<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Shared\Models\Emoji;
use Shared\Enum\LanguageLevel;
use Shared\Enum\SessionStatus;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Application\ReadModels\SessionFlashcardRead;
use Flashcard\Application\ReadModels\SessionFlashcardsRead;
use Flashcard\Infrastructure\Mappers\Traits\HasOwnerBuilder;

class SessionFlashcardReadMapper
{
    use HasOwnerBuilder;

    public function __construct(
        private readonly DB $db,
    ) {}

    public function findUnratedById(SessionId $session_id, int $limit): SessionFlashcardsRead
    {
        $stmt = '
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
                    learning_session_flashcards.exercise_entry_id,
                    flashcards.front_word,
                    flashcards.front_lang,
                    flashcards.back_word,
                    flashcards.back_lang,
                    flashcards.front_context,
                    flashcards.back_context,
                    flashcards.language_level,
                    flashcards.emoji,
                    flashcards.user_id,
                    flashcards.admin_id
                FROM 
                    learning_session_flashcards
                LEFT JOIN
                    flashcards ON flashcards.id = learning_session_flashcards.flashcard_id
                WHERE 
                    learning_session_flashcards.learning_session_id = ?
                    AND learning_session_flashcards.rating IS NULL
                    AND learning_session_flashcards.is_additional = false
                LIMIT ?
            ),
            progress_count AS (
                SELECT 
                    COUNT(learning_session_flashcards.id) AS count 
                FROM 
                    learning_session_flashcards 
                WHERE 
                    learning_session_id = ?
                    AND rating IS NOT NULL
                    AND is_additional = false
            )
            SELECT 
                flashcards_data.id, 
                flashcards_data.rating,
                flashcards_data.exercise_entry_id,
                flashcards_data.front_word,
                flashcards_data.front_lang,
                flashcards_data.back_word,
                flashcards_data.back_lang,
                flashcards_data.front_context,
                flashcards_data.back_context,
                flashcards_data.language_level,
                flashcards_data.emoji,
                flashcards_data.user_id as flashcard_user_id,
                flashcards_data.admin_id as flashcard_admin_id,
                session_data.id AS session_id,
                session_data.status as status,
                session_data.cards_per_session,
                session_data.user_id,
                progress_count.count as progress
            FROM 
                session_data
            LEFT JOIN flashcards_data on true
            LEFT JOIN progress_count on true;
        ';

        $results = $this->db::select($stmt, [
            $session_id->getValue(),
            $session_id->getValue(),
            $limit,
            $session_id->getValue(),
        ]);

        $session_flashcards = array_filter($results, function (object $result) {
            return $result->id !== null && $result->exercise_entry_id === null;
        });

        $session_flashcards = array_map(function (object $result) {
            return $this->map($result);
        }, $session_flashcards);

        $exercise_entry_ids = array_filter($results, function (object $result) {
            return $result->id !== null && $result->exercise_entry_id !== null;
        });

        return new SessionFlashcardsRead(
            $session_id,
            $results[0]->progress,
            $results[0]->cards_per_session,
            SessionStatus::from($results[0]->status) === SessionStatus::FINISHED,
            $session_flashcards,
            array_map(fn($result) => $result->exercise_entry_id, $exercise_entry_ids),
        );
    }

    private function map(object $data): SessionFlashcardRead
    {
        return new SessionFlashcardRead(
            new SessionFlashcardId($data->id),
            $data->front_word,
            Language::from($data->front_lang),
            $data->back_word,
            Language::from($data->back_lang),
            $data->front_context,
            $data->back_context,
            LanguageLevel::from($data->language_level),
            $data->emoji ? Emoji::fromUnicode($data->emoji) : null,
            $this->buildOwner($data->flashcard_user_id, $data->flashcard_admin_id)->getOwnerType(),
        );
    }
}
