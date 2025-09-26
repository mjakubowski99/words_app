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
        $answer_options = [];
        foreach ($story->getStoryFlashcards() as $flashcard) {
            $answer_options[] = $flashcard->getFlashcard()->getBackWord();
        }
        foreach ($options as $option) {
            $answer_options[] = $option->getBackWord();
        }
        $answer_options[] = $base_story_flashcard->getBackWord();
        $answer_options = array_values(array_unique($answer_options));

        for($i=0; $i<count($answer_options); $i++) {
            $answer_options[$i] = new AnswerOption($answer_options[$i]);
        }

        shuffle($answer_options);

        $i = -1;
        return new self(
            $story->getId(),
            array_map(function (StoryFlashcard $story_flashcard) use (&$i) {
                ++$i;

                return new SessionFlashcardSummary(
                    $i,
                    $story_flashcard->getFlashcard(),
                    false,
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
        $answer_options = [];
        foreach ($options as $option) {
            $answer_options[] = $option->getBackWord();
        }
        foreach ($flashcards as $flashcard) {
            $answer_options[] = $flashcard->getBackWord();
        }
        $answer_options[] = $base_flashcard->getBackWord();
        $answer_options = array_values(array_unique($answer_options));

        for($i=0; $i<count($answer_options); $i++) {
            $answer_options[$i] = new AnswerOption($answer_options[$i]);
        }

        shuffle($answer_options);

        $i = -1;
        return new self(
            null,
            array_map(function (Flashcard $flashcard) use (&$i) {
                ++$i;

                return new SessionFlashcardSummary(
                    $i,
                    $flashcard,
                    false,
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
