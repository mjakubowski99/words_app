<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories\Postgres;

use Flashcard\Application\Repository\ISessionRepository;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Mappers\Postgres\SessionMapper;
use Shared\Enum\SessionStatus;

class SessionRepository implements ISessionRepository
{
    public function __construct(
        private readonly SessionMapper $session_mapper,
    ) {}

    public function setAllOwnerSessionsStatus(Owner $owner, SessionStatus $status): void
    {
        $this->session_mapper->updateStatus($owner, $status);
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
}
