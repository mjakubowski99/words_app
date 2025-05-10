<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Factories;

use Shared\Enum\ExerciseType;
use Flashcard\Domain\ValueObjects\SessionId;
use Illuminate\Http\Resources\Json\JsonResource;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Flashcard\Application\ReadModels\ExerciseSummary;
use Flashcard\Application\Query\GetNextSessionFlashcardsHandler;
use Flashcard\Infrastructure\Http\Resources\v2\NextSessionFlashcardsResource;
use Flashcard\Infrastructure\Http\Resources\v2\UnscrambleWordExerciseResource;

class NextSessionFlashcardResourceFactory
{
    public function __construct(
        private GetNextSessionFlashcardsHandler $query,
        private IExerciseReadFacade $exercise_read_facade,
    ) {}

    public function make(SessionId $id, int $limit): NextSessionFlashcardsResource
    {
        $flashcards = $this->query->handle($id, $limit);

        return new NextSessionFlashcardsResource([
            'flashcards' => $flashcards,
            'exercises' => array_map(function (ExerciseSummary $summary) use ($id) {
                $resource = $this->resolveExerciseResource($summary);

                return [
                    'type' => $summary->getExerciseType()->value,
                    'resource' => $resource,
                    'links' => [
                        'next' => route('v2.flashcards.session.get', ['session_id' => $id]),
                        'answer' => route('v2.exercises.unscramble-words.answer', ['exercise_entry_id' => $summary->getExerciseEntryId()]),
                        'skip' => route('v2.exercises.unscramble-words.skip', ['exercise_id' => $resource->resource->getId()->getValue()]),
                    ],
                ];
            }, $flashcards->getExerciseSummaries()),
        ]);
    }

    private function resolveExerciseResource(ExerciseSummary $summary): JsonResource
    {
        return match ($summary->getExerciseType()) {
            /* @phpstan-ignore-next-line */
            ExerciseType::UNSCRAMBLE_WORDS => new UnscrambleWordExerciseResource(
                $this->exercise_read_facade->getUnscrambleWordExercise($summary->getExerciseEntryId()),
            ),
            default => throw new \UnexpectedValueException('Unsupported exercise type'),
        };
    }
}
