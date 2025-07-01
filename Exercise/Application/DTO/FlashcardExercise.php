<?php

namespace Exercise\Application\DTO;

use Exercise\Domain\Models\Exercise;
use Shared\Exercise\IFlashcardExercise;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class FlashcardExercise implements IFlashcardExercise
{
    public function __construct(
        private readonly ExerciseEntryId $id,
        private readonly int $flashcard_id,
    ) {}

    public static function newCollection(ISessionFlashcardSummaries $summaries, Exercise $exercise): array
    {
        $i = 0;
        $items = [];
        foreach ($exercise->getExerciseEntries() as $entry) {
            $items[] = new self(
                $entry->getId(),
                $summaries->getSummaries()[$i]->getFlashcardId(),
            );
        }
        return $items;
    }

    public function getExerciseEntryId(): ExerciseEntryId
    {
        return $this->id;
    }

    public function getFlashcardId(): int
    {
        return $this->flashcard_id;
    }
}