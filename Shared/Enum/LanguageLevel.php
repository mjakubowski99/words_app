<?php

declare(strict_types=1);

namespace Shared\Enum;

enum LanguageLevel: string
{
    public const DEFAULT = 'B2';

    case A1 = 'A1';
    case A2 = 'A2';
    case B1 = 'B1';

    case B2 = 'B2';

    case C1 = 'C1';

    case C2 = 'C2';

    public static function default(): self
    {
        return self::from(self::DEFAULT);
    }
}
