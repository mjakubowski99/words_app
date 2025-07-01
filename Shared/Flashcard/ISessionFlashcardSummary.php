<?php

declare(strict_types=1);

namespace Shared\Flashcard;

use Shared\Models\Emoji;
use Shared\Utils\ValueObjects\Language;

interface ISessionFlashcardSummary
{
    public function getFlashcardId(): int;

    public function getFrontWord(): string;

    public function getBackWord(): string;

    public function getFrontContext(): string;

    public function getBackContext(): string;

    public function getFrontLang(): Language;

    public function getBackLang(): Language;

    public function getEmoji(): ?Emoji;

    public function getIsStoryPart(): bool;

    public function getStorySentence(): ?string;
}
