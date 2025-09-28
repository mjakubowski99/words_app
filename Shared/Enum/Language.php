<?php

declare(strict_types=1);

namespace Shared\Enum;

use Shared\Exceptions\NotFoundException;

enum Language: string
{
    case PL = 'pl';
    case EN = 'en';
    case IT = 'it';
    case ES = 'es';
    case FR = 'fr';
    case DE = 'de';
    case ZH = 'zh';
    case CS = 'cs';

    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public static function defaultUserLanguage(): self
    {
        return self::PL;
    }

    public static function defaultLearnedLanguage(): self
    {
        return self::PL;
    }

    public function getName(): string
    {
        return match ($this) {
            self::PL => 'Polish',
            self::EN => 'English',
            self::IT => 'Italian',
            self::ES => 'Spanish',
            self::FR => 'French',
            self::DE => 'German',
            self::ZH => 'Chinese',
            /* @phpstan-ignore-next-line */
            self::CS => 'Czech',
            default => throw new NotFoundException('Unknown language'),
        };
    }
}
