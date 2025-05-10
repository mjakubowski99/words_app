<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Factories;

use Flashcard\Application\Query\GetNextSessionFlashcardsHandler;
use Flashcard\Application\ReadModels\ExerciseSummary;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Http\Resources\v2\NextSessionFlashcardsResource;
use Flashcard\Infrastructure\Http\Resources\v2\UnscrambleWordExerciseResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Shared\Enum\ExerciseType;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Shared\Exercise\IFlashcardExerciseFacade;

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
            'exercises' => array_map(function (ExerciseSummary $summary) {
                $resource = $this->resolveExerciseResource($summary);

                return [
                    'type' => $summary->getExerciseType()->value,
                    'resource' => $resource,
                ];
            }, $flashcards->getExerciseSummaries()),
        ]);
    }

    private function resolveExerciseResource(ExerciseSummary $summary): JsonResource
    {
        return match ($summary->getExerciseType()) {
            ExerciseType::UNSCRAMBLE_WORDS => new UnscrambleWordExerciseResource(
                $this->exercise_read_facade->getUnscrambleWordExercise($summary->getExerciseEntryId()),
            ),
            default => throw new \UnexpectedValueException('Unsupported exercise type'),
        };
    }
}
