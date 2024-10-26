<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Flashcard\Domain\Models\Owner;
use Illuminate\Support\Facades\DB;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\Models\Category;
use Shared\Exceptions\NotFoundException;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Domain\Models\NextSessionFlashcards;

class NextSessionFlashcardsMapper
{
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
                        ls.cards_per_session,
                        ls.status AS session_status,
                        fc.id AS category_id,
                        fc.name AS category_name,
                        fc.tag AS category_tag
                    FROM 
                        learning_sessions AS ls
                    LEFT JOIN 
                        flashcard_categories AS fc ON fc.id = ls.flashcard_category_id
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
                    GROUP BY 
                        lsf.learning_session_id
                )
                SELECT 
                    sd.session_id,
                    sd.user_id,
                    sd.cards_per_session,
                    sd.session_status,
                    sd.category_id,
                    sd.category_name,
                    sd.category_tag,
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

        $owner = $this->mapOwner($result);

        $category = $this->mapCategory($owner, $result);

        return new NextSessionFlashcards(
            $id,
            $owner,
            $category,
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
            ];
        }

        $this->db::table('learning_session_flashcards')->insert($insert_data);
    }

    private function mapOwner(object $result): Owner
    {
        return new Owner(new OwnerId($result->user_id), FlashcardOwnerType::USER);
    }

    private function mapCategory(Owner $owner, object $result): ?Category
    {
        $category = $result->category_id ? new Category(
            $owner,
            $result->category_tag,
            $result->category_name
        ) : null;
        $category?->init(new CategoryId($result->category_id));

        return $category;
    }
}
