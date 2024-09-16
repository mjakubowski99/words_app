<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers;

use Shared\Enum\SessionStatus;
use Illuminate\Support\Facades\DB;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\ReadModels\SessionRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;

class SessionReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function find(SessionId $id): SessionRead
    {
        $result = $this->db::table('learning_sessions')
            ->where('learning_sessions.id', $id->getValue())
            ->first();

        if (!$result) {
            throw new ModelNotFoundException(SessionRead::class, (string) $id->getValue());
        }

        $rated = $this->db::table('learning_session_flashcards')
            ->where('learning_session_flashcards.learning_session_id', $id)
            ->whereNotNull('rating')
            ->count();

        return $this->map($result, $rated);
    }

    public function map(object $data, int $rated): SessionRead
    {
        return new SessionRead(
            new SessionId($data->id),
            $rated,
            $data->cards_per_session,
            SessionStatus::from($data->status) === SessionStatus::FINISHED,
        );
    }
}
