<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Factories;

use Flashcard\Application\Query\GetNextSessionFlashcardsHandler;
use Flashcard\Domain\ValueObjects\SessionId;
use Flashcard\Infrastructure\Http\Resources\v2\NextSessionFlashcardsResource;
use Flashcard\Infrastructure\Http\Resources\v2\UnscrambleWordExerciseResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Shared\Enum\ExerciseType;
use Shared\Exercise\IExerciseSummary;
use Shared\Exercise\IFlashcardExerciseFacade;

class NextSessionFlashcardResourceFactory
{
    public function __construct(
        private GetNextSessionFlashcardsHandler $query,
        private IFlashcardExerciseFacade $facade
    ) {}

    public function make(SessionId $id, int $limit): NextSessionFlashcardsResource
    {
        $flashcards = $this->query->handle($id, $limit);

        $summaries = [];

        foreach ($flashcards->getExerciseEntryIds() as $entry_id) {
            $summaries[] = $this->facade->getExerciseSummaryByEntryId($entry_id);
        }

        return new NextSessionFlashcardsResource([
            'flashcards' => $flashcards,
            'exercises' => array_map(function (IExerciseSummary $summary) {
                $resource = $this->resolveExerciseResource($summary);

                return [
                    'type' => $summary->getExerciseType()->value,
                    'resource' => $resource,
                ];
            }, $summaries),
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
