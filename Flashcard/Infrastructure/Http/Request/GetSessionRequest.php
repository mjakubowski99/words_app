<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request;

use Shared\Http\Request\Request;
use Flashcard\Domain\Models\SessionId;

class GetSessionRequest extends Request
{
    public function getSessionId(): SessionId
    {
        return new SessionId((int) $this->route('session_id'));
    }
}
