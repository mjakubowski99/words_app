<?php

declare(strict_types=1);

namespace Shared\Utils\ValueObjects;

use Shared\Enum\LanguageLevel;
use Shared\Enum\Language as LanguageEnum;

class Language
{
    public const LANGUAGE_LEVELS = [
        LanguageEnum::EN->value => [
            LanguageLevel::A1,
            LanguageLevel::A2,
            LanguageLevel::B1,
            LanguageLevel::B2,
            LanguageLevel::C1,
            LanguageLevel::C2,
        ],
        LanguageEnum::PL->value => [
            LanguageLevel::A1,
            LanguageLevel::A2,
            LanguageLevel::B1,
            LanguageLevel::B2,
            LanguageLevel::C1,
            LanguageLevel::C2,
        ],
    ];

    private LanguageEnum $value;

    public function __construct(string $value)
    {
        $this->value = LanguageEnum::from($value);
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public static function pl(): self
    {
        return self::from(LanguageEnum::PL->value);
    }

    public static function en(): self
    {
        return self::from(LanguageEnum::EN->value);
    }

    public function getValue(): string
    {
        return $this->value->value;
    }

    /** @return LanguageLevel[] */
    public function getAvailableLanguages(): array
    {
        return self::LANGUAGE_LEVELS[$this->value->value];
    }

    public function __toString(): string
    {
        return $this->value->value;
    }
}
