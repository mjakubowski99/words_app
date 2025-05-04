<?php

declare(strict_types=1);

namespace Shared\Exercise;

interface IUnscrambleWordExercise
{
    public function getExerciseEntryId(): int;

    public function getWord(): string;

    public function getContext(): string;

    public function getScrambledWord(): string;
}
