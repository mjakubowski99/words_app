<?php

declare(strict_types=1);

namespace Shared\Enum;

enum SessionStatus: string
{
    case STARTED = 'started';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';

    public static function activeStatuses(): array
    {
        return [
            self::STARTED->value,
            self::IN_PROGRESS->value,
        ];
    }
}
