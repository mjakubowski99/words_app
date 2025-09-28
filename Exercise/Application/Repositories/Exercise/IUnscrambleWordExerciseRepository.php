<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories\Exercise;

use Shared\Utils\ValueObjects\ExerciseId;
use Shared\Utils\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\Exercise\UnscrambleWordsExercise;

interface IUnscrambleWordExerciseRepository
{
    public function find(ExerciseId $id): UnscrambleWordsExercise;

    public function findByEntryId(ExerciseEntryId $id): UnscrambleWordsExercise;

    public function create(UnscrambleWordsExercise $exercise): ExerciseId;

    public function save(UnscrambleWordsExercise $exercise): void;
}
