<?php

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\Flashcard;
use Flashcard\Domain\Models\Story;
use Flashcard\Domain\Models\StoryFlashcard;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\StoryId;

class SessionFlashcardSummaries implements ISessionFlashcardSummaries
{
    public function __construct(
        private ?StoryId $story_id,
        private array $summaries
    ) {}

    public static function fromStory(Story $story, Flashcard $base_story_flashcard): self
    {
        return new self(
            $story->getId(),
            array_map(fn(StoryFlashcard $story_flashcard) => new SessionFlashcardSummary(
                $story_flashcard->getFlashcard(),
                !$story_flashcard->getFlashcard()->getId()->equals($base_story_flashcard->getId()),
                true,
                $story_flashcard->getSentenceOverride(),
            ), $story->getStoryFlashcards())
        );
    }

    public static function fromFlashcards(array $flashcards, Flashcard $base_flashcard): self
    {
        return new self(
            null,
            array_map(fn(Flashcard $flashcard) => new SessionFlashcardSummary(
                $flashcard,
                !$flashcard->getId()->equals($base_flashcard->getId()),
                true,
                null,
            ), $flashcards)
        );
    }

    public function hasStory(): bool
    {
        return $this->story_id !== null;
    }

    public function getStoryId(): ?StoryId
    {
        return $this->story_id;
    }

    /** @return SessionFlashcardSummary[] */
    public function getSummaries(): array
    {
        return $this->summaries;
    }
}