<?php

namespace Flashcard\Infrastructure\Repositories;

use Flashcard\Application\Repository\INextSessionFlashcardsRepository;
use Flashcard\Domain\Models\NextSessionFlashcards;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Mappers\SessionMapper;
use Illuminate\Support\Facades\DB;

class NextSessionFlashcardsRepository implements INextSessionFlashcardsRepository
{
    public function __construct(
        private readonly DB $db,
        private readonly SessionMapper $session_mapper,
    ) {}

    public function find(SessionId $id): NextSessionFlashcards
    {
        $session = $this->session_mapper->find($id);

        $count = $this->db::table('learning_session_flashcards')
            ->where('learning_session_id', $id->getValue())
            ->count();

        return new NextSessionFlashcards(
            $id,
            $session->getOwner(),
            $session->getFlashcardCategory(),
            $count,
            $session->getCardsPerSession(),
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