<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Mappers\Postgres;

use Flashcard\Application\ReadModels\SessionRead;
use Flashcard\Domain\Exceptions\ModelNotFoundException;
use Flashcard\Domain\ValueObjects\SessionId;
use Illuminate\Support\Facades\DB;
use Shared\Enum\SessionStatus;

class SessionReadMapper
{
    public function __construct(
        private readonly DB $db,
    ) {}

    public function find(SessionId $id): SessionRead
    {
        $result = $this->db::table('learning_sessions')
            ->where('learning_sessions.id', $id->getValue())
            ->selectRaw('
                learning_sessions.*,
                (
                    SELECT COUNT(id) FROM learning_session_flashcards 
                    WHERE learning_session_id = learning_sessions.id
                    and rating is not null
                ) as flashcards_count
            ')
            ->first();

        if (!$result) {
            throw new ModelNotFoundException('Session not found exception');
        }

        return $this->map($result, $result->flashcards_count);
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
