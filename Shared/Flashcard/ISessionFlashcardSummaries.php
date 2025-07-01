<?php

namespace Shared\Flashcard;

use Shared\Utils\ValueObjects\StoryId;

interface ISessionFlashcardSummaries
{
    public function hasStory(): bool;

    public function getStoryId(): ?StoryId;

    /** @return ISessionFlashcardSummary[] */
    public function getSummaries(): array;
}