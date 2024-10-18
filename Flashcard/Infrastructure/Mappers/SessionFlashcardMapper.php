<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Illuminate\Support\Facades\DB;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Domain\ValueObjects\FlashcardId;

class SessionFlashcardMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function getLatestSessionFlashcardIds(SessionId $session_id, int $limit): array
    {
        $latest = $this->db::table('learning_session_flashcards')
            ->where('learning_session_flashcards.learning_session_id', $session_id)
            ->latest()
            ->take($limit)
            ->select('(DISTINCT flashcard_id) as flashcard_id')
            ->get()
            ->pluck('flashcard_id')
            ->toArray();

        return array_map(fn ($id) => new FlashcardId($id), $latest);
    }
}
