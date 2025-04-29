<?php

namespace Exercise\Application\Command;

use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Domain\ValueObjects\ExerciseId;
use Shared\Flashcard\IFlashcardFacade;

class SkipUnscrambleWordExerciseHandler
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $repository,
        private IFlashcardFacade $facade,
    ) {}

    public function handle(ExerciseId $id): void
    {
        $exercise = $this->repository->find($id);

        $exercise->skipExercise();

        $this->repository->save($exercise);

        $this->facade->updateRatingsByPreviousRates(
            $exercise->getExerciseEntries()->pluck('session_flashcard_id'),
        );
    }
}