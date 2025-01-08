<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Application\Repository\ISessionRepository;
use Flashcard\Infrastructure\Mappers\Postgres\SessionMapper;

class SessionRepository implements ISessionRepository
{
    public function __construct(
        private readonly SessionMapper $session_mapper,
    ) {}

    public function setAllOwnerSessionsStatus(UserId $user_id, SessionStatus $status): void
    {
        $this->session_mapper->updateStatus($user_id, $status);
    }

    public function create(Session $session): SessionId
    {
        return $this->session_mapper->create($session);
    }

    public function update(Session $session): void
    {
        $this->session_mapper->update($session);
    }

    public function find(SessionId $id): Session
    {
        return $this->session_mapper->find($id);
    }

    public function deleteAllForUser(UserId $user_id): void
    {
        $this->session_mapper->deleteAllForUser($user_id);
    }
}
