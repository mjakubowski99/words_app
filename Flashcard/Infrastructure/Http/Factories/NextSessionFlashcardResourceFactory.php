<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Factories;

use Shared\Enum\ExerciseType;
use Shared\Exercise\IExerciseSummary;
use Flashcard\Domain\ValueObjects\SessionId;
use Shared\Exercise\IFlashcardExerciseFacade;
use Illuminate\Http\Resources\Json\JsonResource;
use Flashcard\Application\Query\GetNextLearningExerciseQuery;
use Flashcard\Infrastructure\Http\Resources\v2\NextSessionFlashcardsResource;
use Flashcard\Infrastructure\Http\Resources\v2\UnscrambleWordExerciseResource;

class NextSessionFlashcardResourceFactory
{
    public function __construct(
        private GetNextLearningExerciseQuery $query,
        private IFlashcardExerciseFacade $facade
    ) {}

    public function make(SessionId $id, int $limit): NextSessionFlashcardsResource
    {
        $exercises = $this->query->get($id, $limit);

        return new NextSessionFlashcardsResource([
            'flashcards' => $exercises->getSessionFlashcards(),
            'exercises' => array_map(function (IExerciseSummary $summary) {
                $resource = $this->resolveExerciseResource($summary);

                return [
                    'type' => $summary->getExerciseType()->value,
                    'resource' => $resource,
                ];
            }, $exercises->getExerciseSummaries()),
        ]);
    }

    private function resolveExerciseResource(IExerciseSummary $summary): JsonResource
    {
        switch ($summary->getExerciseType()) {
            case ExerciseType::UNSCRAMBLE_WORDS:
                return new UnscrambleWordExerciseResource(
                    $this->facade->getUnscrambleWordExercise($summary->getId())
                );

            default:
                throw new \UnexpectedValueException('Unsupported exercise type');
        }
    }
}
