<?php

declare(strict_types=1);

namespace Flashcard\Application\ReadModels;

use Shared\Models\Emoji;
use Shared\Enum\LanguageLevel;
use Shared\Enum\FlashcardOwnerType;
use Shared\Utils\ValueObjects\Language;
use Flashcard\Domain\ValueObjects\FlashcardId;

class FlashcardRead
{
    public function __construct(
        private FlashcardId $id,
        private string $front_word,
        private Language $front_lang,
        private string $back_word,
        private Language $back_lang,
        private string $front_context,
        private string $back_context,
        private GeneralRating $general_rating,
        private LanguageLevel $language_level,
        private float $rating_percentage,
        private ?Emoji $emoji,
        private FlashcardOwnerType $owner_type,
    ) {}

    public function getId(): FlashcardId
    {
        return $this->id;
    }

    public function getFrontWord(): string
    {
        return $this->front_word;
    }

    public function getFrontLang(): Language
    {
        return $this->front_lang;
    }

    public function getBackWord(): string
    {
        return $this->back_word;
    }

    public function getBackLang(): Language
    {
        return $this->back_lang;
    }

    public function getFrontContext(): string
    {
        return $this->front_context;
    }

    public function getBackContext(): string
    {
        return $this->back_context;
    }

    public function getGeneralRating(): GeneralRating
    {
        return $this->general_rating;
    }

    public function getLanguageLevel(): LanguageLevel
    {
        return $this->language_level;
    }

    public function getRatingPercentage(): float
    {
        return $this->rating_percentage;
    }

    public function getEmoji(): ?Emoji
    {
        return $this->emoji;
    }

    public function getOwnerType(): FlashcardOwnerType
    {
        return $this->owner_type;
    }
}
