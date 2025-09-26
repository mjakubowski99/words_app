<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories\Exercise;

use Exercise\Domain\Models\Exercise\WordMatchExercise;
use Shared\Utils\ValueObjects\ExerciseId;

interface IWordMatchExerciseRepository
{
    public function find(ExerciseId $id): WordMatchExercise;

    public function create(WordMatchExercise $exercise): ExerciseId;

    public function save(WordMatchExercise $exercise): void;
}
