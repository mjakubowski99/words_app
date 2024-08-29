<?php

namespace Flashcard\Infrastructure\DatabaseRepositories;

use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;
use Flashcard\Infrastructure\DatabaseMappers\FlashcardMapper;
use Flashcard\Infrastructure\DatabaseMappers\SessionFlashcardMapper;
use Illuminate\Support\Facades\DB;

class SessionFlashcardRepository extends AbstractRepository implements ISessionFlashcardRepository
{
    public function __construct(
        private readonly DB $db,
        private readonly SessionFlashcardMapper $session_flashcard_mapper,
    ) {}

    public function getNotRatedSessionFlashcards(SessionId $session_id, int $limit): array
    {
        $this->db::table('learning_session_flashcards')
            ->join('learning_sessions', 'learning_sessions.id', '=', 'session_flashcards.session_id')
            ->where('session_id', $session_id->getValue())
            ->take($limit)
            ->select(
                ...$this->dbPrefix('flashcards', FlashcardMapper::COLUMNS),
                ...$this->dbPrefix('learning_session_flashcards', SessionFlashcardMapper::COLUMNS)
            )
            ->get()
            ->map(function (object $data) {
                $data = (array) $data;

                return $this->session_flashcard_mapper->map($data);
            })->toArray();
    }

    public function addFlashcardsToSession(SessionId $session_id, array $flashcards): void
    {
        $insert_data = [];
        $now = now();

        foreach ($flashcards as $flashcard) {
            $insert_data[] = [
                'session_id' => $session_id->getValue(),
                'flashcard_id' => $flashcard->getId(),
                'rating' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->db::table('session_flashcards')->insert($insert_data);
    }
}