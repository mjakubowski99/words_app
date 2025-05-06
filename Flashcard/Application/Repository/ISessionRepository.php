<?php

declare(strict_types=1);

namespace Flashcard\Application\Repository;

use Shared\Enum\SessionStatus;
use Flashcard\Domain\Models\Session;
use Shared\Utils\ValueObjects\UserId;
use Flashcard\Domain\ValueObjects\SessionId;

interface ISessionRepository
{
    /** @param SessionId[] $session_ids */
    public function updateStatusById(array $session_ids, SessionStatus $status): void;
    public function setAllOwnerSessionsStatus(UserId $user_id, SessionStatus $status): void;

    public function create(Session $session): SessionId;

    public function update(Session $session): void;

    public function find(SessionId $id): Session;

    public function deleteAllForUser(UserId $user_id): void;

    public function hasAnySession(UserId $user_id): bool;
}
