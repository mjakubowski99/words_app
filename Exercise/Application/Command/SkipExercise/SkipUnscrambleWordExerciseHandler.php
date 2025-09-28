<?php

declare(strict_types=1);

namespace Exercise\Application\Command\SkipExercise;

use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\Exercise\Exercise;
use Exercise\Domain\Exceptions\InvalidExerciseTypeException;
use Exercise\Domain\Models\Exercise\UnscrambleWordsExercise;
use Exercise\Application\Repositories\Exercise\IUnscrambleWordExerciseRepository;

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
