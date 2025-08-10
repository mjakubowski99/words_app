<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Factories;

use Shared\Enum\ExerciseType;
use Flashcard\Domain\ValueObjects\SessionId;
use Illuminate\Http\Resources\Json\JsonResource;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Flashcard\Application\ReadModels\ExerciseSummary;
use Flashcard\Application\Query\GetNextSessionFlashcardsHandler;
use Flashcard\Infrastructure\Http\Resources\v2\WordMatchExerciseResource;
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
                    // @TODO organize this better
                    'links' => match ($summary->getExerciseType()) {
                        ExerciseType::UNSCRAMBLE_WORDS => [
                            'next' => route('v2.flashcards.session.get', ['session_id' => $id]),
                            'answer' => route('v2.exercises.unscramble-words.answer', ['exercise_entry_id' => $summary->getExerciseEntryId()]),
                            'skip' => route('v2.exercises.unscramble-words.skip', ['exercise_id' => $resource->resource->getId()->getValue()]),
                        ],
                        /* @phpstan-ignore-next-line */
                        ExerciseType::WORD_MATCH => [
                            'next' => route('v2.flashcards.session.get', ['session_id' => $id]),
                            'answer' => route('v2.exercises.word-match.answer', ['exercise_id' => $resource->resource->getExerciseId()->getValue()]),
                            'skip' => route('v2.exercises.word-match.skip', ['exercise_id' => $resource->resource->getExerciseId()->getValue()]),
                        ],
                        default => throw new \UnexpectedValueException('Unsupported exercise type'),
                    },
                ];
            }, $flashcards->getExerciseSummaries()),
        ]);
    }

    private function resolveExerciseResource(ExerciseSummary $summary): JsonResource
    {
        return match ($summary->getExerciseType()->value) {
            ExerciseType::UNSCRAMBLE_WORDS->value => new UnscrambleWordExerciseResource(
                $this->exercise_read_facade->getUnscrambleWordExercise($summary->getExerciseEntryId()),
            ),
            /* @phpstan-ignore-next-line */
            ExerciseType::WORD_MATCH->value => new WordMatchExerciseResource(
                $this->exercise_read_facade->getWordMatchExercise($summary->getExerciseEntryId())
            ),
            default => throw new \UnexpectedValueException('Unsupported exercise type'),
        };
    }
}
