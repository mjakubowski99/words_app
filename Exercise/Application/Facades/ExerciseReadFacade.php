<?php

declare(strict_types=1);

namespace Exercise\Application\Facades;

use Exercise\Application\Repositories\IWordMatchExerciseReadRepository;
use Shared\Exercise\Exercises\IWordMatchExerciseRead;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Shared\Exercise\Exercises\IUnscrambleWordExerciseRead;
use Exercise\Application\Repositories\IUnscrambleWordExerciseReadRepository;
use Shared\Utils\ValueObjects\ExerciseId;

class ExerciseReadFacade implements IExerciseReadFacade
{
    public function __construct(
        private IUnscrambleWordExerciseReadRepository $unscramble_word_repository,
        private IWordMatchExerciseReadRepository $word_match_repository,

    ) {}

    public function getUnscrambleWordExercise(ExerciseEntryId $id): IUnscrambleWordExerciseRead
    {
        return $this->unscramble_word_repository->findByEntryId($id);
    }

    public function getWordMatchExercise(ExerciseEntryId $id): IWordMatchExerciseRead
    {
        return $this->word_match_repository->findByEntryId($id);
    }
}
