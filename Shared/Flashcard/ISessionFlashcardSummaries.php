<?php

declare(strict_types=1);

namespace Shared\Flashcard;

use Shared\Utils\ValueObjects\StoryId;

interface ISessionFlashcardSummaries
{
    public function hasStory(): bool;

    public function getStoryId(): ?StoryId;

    /** @return ISessionFlashcardSummary[] */
    public function getSummaries(): array;
    /** @return IAnswerOption[] */
    public function getAnswerOptions(): array;
}
