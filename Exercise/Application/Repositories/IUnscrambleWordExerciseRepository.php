<?php

declare(strict_types=1);

namespace Exercise\Application\Repositories;

use Shared\Utils\ValueObjects\ExerciseId;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\Models\UnscrambleWordsExercise;

interface IUnscrambleWordExerciseRepository
{
    public function find(ExerciseId $id): UnscrambleWordsExercise;

    public function findByEntryId(ExerciseEntryId $id): UnscrambleWordsExercise;

    public function create(UnscrambleWordsExercise $exercise): ExerciseId;

    public function save(UnscrambleWordsExercise $exercise): void;
}
