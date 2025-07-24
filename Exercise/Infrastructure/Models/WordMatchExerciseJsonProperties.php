<?php

declare(strict_types=1);

namespace Exercise\Infrastructure\Models;

use Shared\Utils\ValueObjects\StoryId;
use Exercise\Domain\Models\WordMatchExercise;

class WordMatchExerciseJsonProperties
{
    public function __construct(private array $properties) {}

    public static function fromExercise(WordMatchExercise $exercise): self
    {
        $properties = [
            'story_id' => $exercise->getStoryId()?->getValue(),
            'sentences' => [],
            'answer_options' => [],
        ];

        foreach ($exercise->getExerciseEntries() as $entry) {
            $properties['sentences'][] = [
                'order' => $entry->getOrder(),
                'sentence' => $entry->getSentence(),
                'word' => $entry->getWord(),
                'translation' => $entry->getWordTranslation(),
            ];
        }

        foreach ($exercise->getOptions() as $option) {
            $properties['answer_options'][] = $option;
        }

        return new self($properties);
    }

    public function getStoryId(): ?StoryId
    {
        return $this->properties['story_id'] ? new StoryId($this->properties['story_id']) : null;
    }

    public function getAnswerOptions(): array
    {
        return $this->properties['answer_options'] ?? [];
    }

    public function getSentence(int $order): string
    {
        return $this->properties['sentences'][$order]['sentence'] ?? '';
    }

    public function getWord(int $order): string
    {
        return $this->properties['sentences'][$order]['word'] ?? '';
    }

    public function getTranslation(int $order): string
    {
        return $this->properties['sentences'][$order]['translation'] ?? '';
    }

    public function toJsonArray(): array
    {
        return $this->properties;
    }
}
