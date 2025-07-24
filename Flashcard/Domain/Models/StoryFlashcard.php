<?php

declare(strict_types=1);

namespace Flashcard\Domain\Models;

use Shared\Utils\ValueObjects\StoryId;

class StoryFlashcard
{
    public function __construct(
        private StoryId $story_id,
        private int $story_index,
        private ?string $sentence_override,
        private Flashcard $flashcard,
    ) {}

    public function getIndex(): int
    {
        return $this->story_index;
    }

    public function getFlashcard(): Flashcard
    {
        return $this->flashcard;
    }

    public function setStoryId(StoryId $story_id): void
    {
        $this->story_id = $story_id;
    }

    public function getStoryId(): StoryId
    {
        return $this->story_id;
    }

    public function getSentenceOverride(): ?string
    {
        return $this->sentence_override;
    }
}
