<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\Models\SessionId;

interface ISessionRepository
{
    public function setAllUserSessionsStatus(UserId $user_id, SessionStatus $status): void;

    public function create(Session $session);

    public function find(SessionId $id): Session;
}
