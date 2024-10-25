<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Category;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\ValueObjects\OwnerId;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Shared\Enum\FlashcardOwnerType;

class NextSessionFlashcardsMapper
{
    public function __construct(
        private readonly DB $db,
        private readonly SessionMapper $session_mapper,
    ) {}

    public function find(SessionId $id): NextSessionFlashcards
    {
        $session = $this->session_mapper->find($id);

        $stmt = "
            WITH session_data AS (
                SELECT 
                    learning_sessions.id AS session_id,
                    learning_sessions.user_id as user_id,
                    learning_sessions.cards_per_session AS cards_per_session,
                    learning_sessions.status AS session_status,
                    flashcard_categories.id AS category_id,
                    flashcard_categories.name AS category_name,
                    flashcard_categories.tag AS category_tag
                FROM 
                    learning_sessions
                LEFT JOIN 
                    flashcard_categories ON flashcard_categories.id = learning_sessions.flashcard_category_id
                WHERE 
                    learning_sessions.id = ?
            ),
            all_count AS (
                SELECT 
                    COUNT(id) AS count 
                FROM 
                    learning_session_flashcards
                WHERE 
                    learning_session_id = ?
            ),
            unrated_count AS (
                SELECT 
                    COUNT(id) AS count
                FROM 
                    learning_session_flashcards
                WHERE 
                    learning_session_id = ? AND rating IS NULL 
            )
            SELECT 
                session_data.session_id,
                session_data.user_id,
                session_data.cards_per_session,
                session_data.session_status,
                session_data.category_id,
                session_data.category_name,
                session_data.category_tag,
                unrated_count.count AS unrated_count,
                all_count.count AS all_count
            FROM
                session_data,
                all_count,
                unrated_count;
        ";

        $results = $this->db::select($stmt, [
            $session->getId(),
            $session->getId(),
            $session->getId()
        ]);

        $result = $results[0];

        $owner = new Owner(new OwnerId($result->user_id), FlashcardOwnerType::USER);

        $category = $result->category_id ? new Category(
            $owner,
            $result->category_tag,
            $result->category_name
        ) : null;
        $category?->init(new CategoryId($result->category_id));

        return new NextSessionFlashcards(
            $id,
            new Owner(new OwnerId($result->user_id), FlashcardOwnerType::USER),
            $category,
            $result->all_count,
            $result->unrated_count,
            $result->cards_per_session,
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
            ];
        }

        $this->db::table('learning_session_flashcards')->insert($insert_data);
    }
}
