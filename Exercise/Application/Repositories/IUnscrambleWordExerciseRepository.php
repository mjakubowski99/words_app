<?php

namespace Exercise\Application\Repositories;

use Exercise\Domain\Models\UnscrambleWordsExercise;
use Exercise\Domain\ValueObjects\ExerciseEntryId;
use Exercise\Domain\ValueObjects\ExerciseId;

interface IUnscrambleWordExerciseRepository
{
    public function find(ExerciseId $id): UnscrambleWordsExercise;
    public function findByAnswerEntryId(ExerciseEntryId $id): UnscrambleWordsExercise;
    public function create(UnscrambleWordsExercise $exercise): ExerciseId;
    public function save(UnscrambleWordsExercise $exercise): void;
}