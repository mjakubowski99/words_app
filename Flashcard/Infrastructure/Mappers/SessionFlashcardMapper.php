<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Illuminate\Support\Facades\DB;
use Flashcard\Domain\Models\Rating;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Domain\Models\FlashcardId;
use Flashcard\Domain\Models\SessionFlashcard;
use Flashcard\Domain\Models\SessionFlashcards;
use Flashcard\Domain\Models\SessionFlashcardId;

class SessionFlashcardMapper
{
    public function __construct(
        private readonly DB $db,
        private readonly SessionMapper $session_mapper,
    ) {}

    public function createMany(SessionFlashcards $flashcards): void
    {
        $insert_data = [];
        $now = now();

        /** @var SessionFlashcard $flashcard */
        foreach ($flashcards->all() as $flashcard) {
            $insert_data[] = [
                'learning_session_id' => $flashcards->getSession()->getId(),
                'flashcard_id' => $flashcard->getFlashcardId(),
                'rating' => $flashcard->rated() ? $flashcard->getRating() : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->db::table('learning_session_flashcards')->insert($insert_data);
    }

    public function findFlashcardWithNullRating(SessionId $session_id, int $limit): SessionFlashcards
    {
        $learning_session = $this->session_mapper->find($session_id);

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
                return $this->map($data);
            })->toArray();

        return new SessionFlashcards($learning_session, $results);
    }

    public function findMany(SessionId $session_id, array $session_flashcard_ids): SessionFlashcards
    {
        $learning_session = $this->session_mapper->find($session_id);

        $results = $this->db::table('learning_session_flashcards')
            ->join('learning_sessions', 'learning_session_flashcards.learning_session_id', '=', 'learning_sessions.id')
            ->join('flashcards', 'flashcards.id', '=', 'learning_session_flashcards.flashcard_id')
            ->where('learning_sessions.id', $learning_session->getId())
            ->select(
                'learning_session_flashcards.id',
                'learning_session_flashcards.flashcard_id',
                'learning_session_flashcards.rating'
            )
            ->whereIn('learning_session_flashcards.id', $session_flashcard_ids)
            ->get()
            ->map(function (object $result) {
                return $this->map($result);
            })->toArray();

        return new SessionFlashcards($learning_session, $results);
    }

    public function find(SessionFlashcardId $id): SessionFlashcard
    {
        $result = $this->db::table('learning_session_flashcards')
            ->find($id->getValue());

        return $this->map($result);
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

    public function getNotRatedSessionFlashcardsCount(SessionId $session_id): int
    {
        return $this->db::table('learning_session_flashcards')
            ->where('learning_session_flashcards.learning_session_id', $session_id)
            ->whereNull('learning_session_flashcards.rating')
            ->count();
    }

    public function getRatedSessionFlashcardsCount(SessionId $session_id): int
    {
        return $this->db::table('learning_session_flashcards')
            ->where('learning_session_flashcards.learning_session_id', $session_id)
            ->whereNotNull('learning_session_flashcards.rating')
            ->count();
    }

    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array
    {
        $latest = $this->db::table('learning_session_flashcards')
            ->where('learning_session_flashcards.learning_session_id', $session_id)
            ->latest()
            ->take($limit)
            ->pluck('flashcard_id')
            ->toArray();

        return array_map(fn($id) => new FlashcardId($id), $latest);
    }

    public function getTotalSessionFlashcardsCount(SessionId $session_id): int
    {
        return $this->db::table('learning_session_flashcards')
            ->where('learning_session_flashcards.learning_session_id', $session_id)
            ->count();
    }

    private function map(object $data): SessionFlashcard
    {
        return (new SessionFlashcard(
            new FlashcardId($data->flashcard_id),
            $data->rating !== null ? Rating::from($data->rating) : null,
        ))->init(new SessionFlashcardId($data->id));
    }
}
