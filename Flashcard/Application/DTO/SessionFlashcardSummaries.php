<?php

declare(strict_types=1);

namespace Flashcard\Application\DTO;

use Flashcard\Domain\Models\Story;
use Shared\Flashcard\IAnswerOption;
use Flashcard\Domain\Models\Flashcard;
use Shared\Utils\ValueObjects\StoryId;
use Flashcard\Domain\Models\StoryFlashcard;
use Shared\Flashcard\ISessionFlashcardSummaries;

class SessionFlashcardSummaries implements ISessionFlashcardSummaries
{
    public function __construct(
        private ?StoryId $story_id,
        private array $summaries,
        /** @property IAnswerOption[] $options */
        private array $options = []
    ) {}

    /** @param Flashcard[] $options */
    public static function fromStory(Story $story, Flashcard $base_story_flashcard, array $options = []): self
    {
        $i = -1;

        $answer_options = [];
        foreach ($story->getStoryFlashcards() as $flashcard) {
            $answer_options[] = new AnswerOption($flashcard->getFlashcard()->getBackWord());
        }
        foreach ($options as $option) {
            $answer_options[] = new AnswerOption($option->getBackWord());
        }

        shuffle($answer_options);

        return new self(
            $story->getId(),
            array_map(function (StoryFlashcard $story_flashcard) use (&$i, $base_story_flashcard) {
                ++$i;

                return new SessionFlashcardSummary(
                    $i,
                    $story_flashcard->getFlashcard(),
                    !$story_flashcard->getFlashcard()->getId()->equals($base_story_flashcard->getId()),
                    true,
                    $story_flashcard->getSentenceOverride(),
                );
            }, $story->getStoryFlashcards()),
            $answer_options
        );
    }

    /** @param Flashcard[] $options */
    public static function fromFlashcards(array $flashcards, Flashcard $base_flashcard, array $options = []): self
    {
        $i = -1;

        $answer_options = [];
        foreach ($options as $option) {
            $answer_options[] = new AnswerOption($option->getBackWord());
        }
        foreach ($flashcards as $flashcard) {
            $answer_options[] = new AnswerOption($flashcard->getBackWord());
        }

        shuffle($answer_options);

        return new self(
            null,
            array_map(function (Flashcard $flashcard) use (&$i, $base_flashcard) {
                ++$i;

                return new SessionFlashcardSummary(
                    $i,
                    $flashcard,
                    !$flashcard->getId()->equals($base_flashcard->getId()),
                    false,
                    null,
                );
            }, $flashcards),
            $answer_options
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

    /** @return IAnswerOption[] */
    public function getAnswerOptions(): array
    {
        return $this->options;
    }
}
