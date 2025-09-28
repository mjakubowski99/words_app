<?php

declare(strict_types=1);

namespace Shared\Enum;

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
            self::CS => 'Czech',
        };
    }
}
