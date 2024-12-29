<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\Session;
use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Utils\ValueObjects\UserId;

interface ISessionRepository
{
    public function setAllOwnerSessionsStatus(Owner $owner, SessionStatus $status): void;

    public function create(Session $session): SessionId;

    public function update(Session $session): void;

    public function find(SessionId $id): Session;
    public function deleteAllForUser(UserId $user_id): void;
}
