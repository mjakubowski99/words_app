<?php

declare(strict_types=1);

namespace Auth\Domain\Models\Enum;

enum FailReason: string
{
    // Auth
    case INVALID_LOGIN_CREDENTIALS = 'Invalid login credentials';
}
