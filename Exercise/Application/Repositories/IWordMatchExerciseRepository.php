<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories;

use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\Models\WordMatchExercise;

interface IWordMatchExerciseRepository
{
    public function find(ExerciseId $id): WordMatchExercise;

    public function create(WordMatchExercise $exercise): ExerciseId;

    public function save(WordMatchExercise $exercise): void;
}
