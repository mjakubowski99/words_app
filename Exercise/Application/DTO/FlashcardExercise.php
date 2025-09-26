<?php

declare(strict_types=1);

namespace Exercise\Application\DTO;

use Exercise\Domain\Models\Exercise\Exercise;
use Shared\Exercise\IFlashcardExercise;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\ExerciseEntryId;

class FlashcardExercise implements IFlashcardExercise
{
    public function __construct(
        private readonly ExerciseEntryId $id,
        private readonly int $flashcard_id,
    ) {}

    public static function fromFlashcardSummaries(ISessionFlashcardSummaries $summaries, Exercise $exercise): array
    {
        $items = [];
        foreach ($summaries->getSummaries() as $summary) {
            foreach ($exercise->getExerciseEntries() as $entry) {
                if ($summary->getOrder() === $entry->getOrder()) {
                    $items[] = new self(
                        $entry->getId(),
                        $summary->getFlashcardId(),
                    );

                    break;
                }
            }
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
