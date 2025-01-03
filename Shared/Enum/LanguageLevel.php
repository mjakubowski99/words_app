<?php

declare(strict_types=1);

namespace Shared\Enum;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'LanguageLevel',
    description: 'Language level. Default value is: ' . LanguageLevel::DEFAULT,
    enum: [
        LanguageLevel::A1,
        LanguageLevel::A2,
        LanguageLevel::B1,
        LanguageLevel::B1,
        LanguageLevel::C1,
        LanguageLevel::C2,
    ],
    example: LanguageLevel::C1,
    nullable: true
)]
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

    public static function getForSelectOptions(): array
    {
        $data = [];
        foreach (self::cases() as $case) {
            $data[$case->value] = $case->value;
        }

        return $data;
    }
}
