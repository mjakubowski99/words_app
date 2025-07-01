<?php

namespace Exercise\Application\Repositories;

use Exercise\Domain\Models\WordMatchExercise;
use Shared\Utils\ValueObjects\ExerciseId;

interface IWordMatchExerciseRepository
{
    public function find(ExerciseId $id): WordMatchExercise;
    public function create(WordMatchExercise $exercise): ExerciseId;
    public function save(WordMatchExercise $exercise): void;
}