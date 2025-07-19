<?php

namespace Exercise\Infrastructure\Models;

use Exercise\Domain\Models\WordMatchExercise;
use Shared\Utils\ValueObjects\StoryId;

class WordMatchExerciseJsonProperties
{
    public function __construct(private array $properties) {}

    public static function fromExercise(WordMatchExercise $exercise): self
    {
        $properties = [
            'story_id' => $exercise->getStoryId()?->getValue(),
            'sentences' => [],
        ];

        foreach ($exercise->getExerciseEntries() as $entry) {
            $properties['sentences'][] = [
                'order' => $entry->getOrder(),
                'sentence' => $entry->getSentence(),
                'word' => $entry->getWord(),
                'translation' => $entry->getWordTranslation(),
            ];
        }

        return new self($properties);
    }

    public function getStoryId(): ?StoryId
    {
        return $this->properties['story_id'] ? new StoryId($this->properties['story_id']) : null;
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