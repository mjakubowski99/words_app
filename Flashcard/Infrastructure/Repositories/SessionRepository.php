<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Repositories;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SessionId;
use Flashcard\Infrastructure\Mappers\SessionMapper;
use Flashcard\Domain\Repositories\ISessionRepository;

class SessionRepository implements ISessionRepository
{
    public function __construct(
        private readonly SessionMapper $session_mapper,
    ) {}

    public function setAllUserSessionsStatus(UserId $user_id, SessionStatus $status): void
    {
        $this->session_mapper->updateStatus($user_id, $status);
    }

    public function create(Session $session): SessionId
    {
        return $this->session_mapper->create($session);
    }

    public function find(SessionId $id): Session
    {
        return $this->session_mapper->find($id);
    }
}
