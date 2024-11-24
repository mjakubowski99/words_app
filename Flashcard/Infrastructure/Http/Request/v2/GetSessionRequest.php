<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Request\v2;

use Shared\Http\Request\Request;
use Flashcard\Domain\ValueObjects\SessionId;

class GetSessionRequest extends Request
{
    public function getSessionId(): SessionId
    {
        $session_id = $this->route('session_id');

        if (is_object($session_id)) {
            throw new \UnexpectedValueException('Session id is object');
        }

        return new SessionId((int) $session_id);
    }
}
