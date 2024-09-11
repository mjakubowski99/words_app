<?php

declare(strict_types=1);

namespace Flashcard\Domain\Repositories;

use Flashcard\Domain\Models\Owner;
use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\Models\SessionId;

interface ISessionRepository
{
    public function setAllOwnerSessionsStatus(Owner $owner, SessionStatus $status): void;

    public function create(Session $session): SessionId;

    public function update(Session $session): void;

    public function find(SessionId $id): Session;
}
