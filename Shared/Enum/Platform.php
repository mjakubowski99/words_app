<?php

declare(strict_types=1);

namespace Shared\Enum;

enum Platform: string
{
    case WEB = 'web';
    case ANDROID = 'android';

    case IOS = 'ios';
}
