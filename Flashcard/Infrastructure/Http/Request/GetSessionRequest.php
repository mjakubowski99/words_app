<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Http\Request\Request;

class GetSessionRequest extends Request
{
    public function getSessionId(): SessionId
    {
        return new SessionId((int) $this->route('session_id'));
    }
}
