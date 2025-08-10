<?php

declare(strict_types=1);

namespace Exercise\Application\Command\SkipExercise;

use Exercise\Domain\Models\Exercise;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\Exceptions\InvalidExerciseTypeException;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;

class SkipUnscrambleWordExerciseHandler extends AbstractSkipExerciseHandler
{
    public function __construct(
        private IUnscrambleWordExerciseRepository $repository,
        IFlashcardFacade $facade,
    ) {
        parent::__construct($facade);
    }

    protected function findExercise(ExerciseId $id): Exercise
    {
        return $this->repository->find($id);
    }

    protected function saveExercise(Exercise $exercise): void
    {
        if (!$exercise instanceof UnscrambleWordsExercise) {
            throw new InvalidExerciseTypeException();
        }
        $this->repository->save($exercise);
    }
}
