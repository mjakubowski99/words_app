<?php

declare(strict_types=1);

namespace Exercise\Application\Command\AnswerExercise;

use Exercise\Domain\Exceptions\InvalidExerciseTypeException;
use Exercise\Domain\Models\Exercise;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\WordMatchExercise;
use Exercise\Application\Repositories\IWordMatchExerciseRepository;

class WordMatchExerciseAnswerHandler extends AbstractExerciseAnswerHandler
{
    public function __construct(
        private IWordMatchExerciseRepository $repository,
        IFlashcardFacade $facade,
    ) {
        parent::__construct($facade);
    }

    protected function resolveExercise(ExerciseId $exercise_id): Exercise
    {
        return $this->repository->find($exercise_id);
    }

    protected function save(Exercise $exercise): void
    {
        if (!$exercise instanceof WordMatchExercise) {
            throw new InvalidExerciseTypeException();
        }
        $this->repository->save($exercise);
    }
}
