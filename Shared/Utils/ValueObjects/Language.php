<?php

declare(strict_types=1);

namespace Shared\Utils\ValueObjects;

use Shared\Enum\LanguageLevel;
use Shared\Enum\Language as LanguageEnum;

class Language
{
    public const DEFAULT_LEVELS = [
        LanguageLevel::A1,
        LanguageLevel::A2,
        LanguageLevel::B1,
        LanguageLevel::B2,
        LanguageLevel::C1,
        LanguageLevel::C2,
    ];

    public const LANGUAGE_LEVELS = [
        LanguageEnum::PL->value => self::DEFAULT_LEVELS,
        LanguageEnum::EN->value => self::DEFAULT_LEVELS,
        LanguageEnum::IT->value => self::DEFAULT_LEVELS,
        LanguageEnum::ES->value => self::DEFAULT_LEVELS,
        LanguageEnum::FR->value => self::DEFAULT_LEVELS,
        LanguageEnum::DE->value => self::DEFAULT_LEVELS,
        LanguageEnum::ZH->value => self::DEFAULT_LEVELS,
        LanguageEnum::CS->value => self::DEFAULT_LEVELS,
    ];

    private LanguageEnum $value;

    public static function all(): array
    {
        return array_map(fn ($lang) => new self($lang->value), LanguageEnum::cases());
    }

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

    public static function it(): self
    {
        return self::from(LanguageEnum::IT->value);
    }

    public static function es(): self
    {
        return self::from(LanguageEnum::ES->value);
    }

    public static function fr(): self
    {
        return self::from(LanguageEnum::FR->value);
    }

    public static function de(): self
    {
        return self::from(LanguageEnum::DE->value);
    }

    public static function zh(): self
    {
        return self::from(LanguageEnum::ZH->value);
    }

    public static function cs(): self
    {
        return self::from(LanguageEnum::CS->value);
    }

    public function getEnum(): LanguageEnum
    {
        return $this->value;
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
