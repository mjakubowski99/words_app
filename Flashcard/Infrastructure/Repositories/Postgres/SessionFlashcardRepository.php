<?php

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcardId;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;
use Flashcard\Infrastructure\Repositories\Mappers\FlashcardMapper;
use Flashcard\Infrastructure\Repositories\Mappers\SessionFlashcardMapper;
use Illuminate\Support\Facades\DB;
use Shared\Utils\ValueObjects\UserId;

class SessionFlashcardRepository implements ISessionFlashcardRepository
{
    public function __construct(
        private readonly DB $db,
        private readonly SessionFlashcardMapper $session_flashcard_mapper,
    ) {}

    public function getNotRatedSessionFlashcards(SessionId $session_id, int $limit): SessionFlashcards
    {
        $learning_session = $this->db::table('learning_sessions')->findOrFail($session_id->getValue());

        $results = $this->db::table('learning_session_flashcards')
            ->join('flashcards', 'flashcards.id', '=', 'learning_session_flashcards.flashcard_id')
            ->where('session_id', $session_id->getValue())
            ->take($limit)
            ->select(
                'flashcards.id as flashcards_id',
                'flashcards.word as flashcards_word',
                'flashcards.word_lang as flashcards_word_lang',
                'flashcards.translation as flashcards_translation',
                'flashcards.translation_lang as flashcards_translation_lang',
                'flashcards.context as flashcards_translation_context',
                'flashcards.context_translation as flashcards_translation_context',
                'learning_sessions.flashcard_id as learning_sessions_flashcard_id',
                'learning_sessions.rating as learning_sessions_rating',
            )
            ->get()
            ->map(function (object $data) {
                $data = (array) $data;

                return $this->session_flashcard_mapper->map($data);
            })->toArray();

        return new SessionFlashcards($results, new UserId($learning_session->user_id));
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

    public function findSessionFlashcard(SessionFlashcardId $id): SessionFlashcard
    {
        $result = $this->db::table('learning_session_flashcards')
            ->select(
                'learning_session_flashcards.id as learning_session_flashcards_id',
                'learning_session_flashcards.flashcard_id as learning_session_flashcards_flashcard_id',
                'learning_session_flashcards.rating as learning_session_flashcards_rating',
            )->find($id->getValue());

        return $this->session_flashcard_mapper->map((array) $result);
    }

    public function findMany(UserId $user_id, array $session_flashcard_ids): SessionFlashcards
    {
        $ids = array_map(fn (SessionFlashcardId $id) => $id->getValue(), $session_flashcard_ids);

        $results = $this->db::table('learning_session_flashcards')
            ->join('learning_sessions', 'learning_session_flashcards.learning_session_id', '=', 'learning_sessions.id')
            ->join('flashcards', 'flashcards.id', '=', 'learning_session_flashcards.flashcard_id')
            ->where('learning_sessions.user_id', $user_id->getValue())
            ->select(
                'learning_session_flashcards.id as learning_session_flashcards_id',
                'learning_session_flashcards.flashcard_id as learning_session_flashcards_flashcard_id',
                'learning_session_flashcards.rating as learning_session_flashcards_rating',
            )
            ->whereIn('learning_session_flashcards.id', $ids)
            ->get()
            ->map(function (object $result) {
                return $this->session_flashcard_mapper->map((array) $result);
            })->toArray();

        return new SessionFlashcards($results, $user_id);
    }

    public function saveRating(SessionFlashcards $session_flashcards): void
    {
        /** @var SessionFlashcard $session_flashcard */
        foreach ($session_flashcards->all() as $session_flashcard) {
            $this->db::table('learning_session_flashcards')
                ->where('id', $session_flashcard->getId()->getValue())
                ->update(['rating' => $session_flashcard->getRating()->value]);
        }
    }
}