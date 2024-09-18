<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Illuminate\Support\Facades\DB;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Domain\Models\RateableSessionFlashcard;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Flashcard\Domain\Models\RateableSessionFlashcards;

class RateableSessionFlashcardsMapper
{
    public function __construct(
        private readonly DB $db,
        private readonly SessionMapper $session_mapper,
    ) {}

    public function find(SessionId $id): RateableSessionFlashcards
    {
        $session = $this->session_mapper->find($id);

        $count = $this->db::table('learning_session_flashcards')
            ->where('learning_session_id', $id)
            ->whereNotNull('rating')
            ->count();

        $results = $this->db::table('learning_session_flashcards')
            ->where('learning_session_id', $id)
            ->whereNull('rating')
            ->get()
            ->toArray();

        $results = array_map(fn (object $result) => new RateableSessionFlashcard(
            new SessionFlashcardId($result->id),
            new FlashcardId($result->flashcard_id)
        ), $results);

        return new RateableSessionFlashcards(
            $id,
            $session->getOwner(),
            $session->getStatus(),
            $count,
            $session->getCardsPerSession(),
            $results,
        );
    }

    public function save(RateableSessionFlashcards $flashcards): void
    {
        $session = $this->session_mapper->find($flashcards->getSessionId());

        if ($session->getStatus() !== $flashcards->getStatus()) {
            $session->setStatus($flashcards->getStatus());
            $this->session_mapper->update($session);
        }

        foreach ($flashcards->getRateableSessionFlashcards() as $flashcard) {
            if ($flashcard->rated()) {
                $this->db::table('learning_session_flashcards')
                    ->where('learning_session_id', $flashcards->getSessionId())
                    ->where('id', $flashcard->getId())
                    ->update([
                        'rating' => $flashcard->getRating(),
                    ]);
            }
        }
    }
}
