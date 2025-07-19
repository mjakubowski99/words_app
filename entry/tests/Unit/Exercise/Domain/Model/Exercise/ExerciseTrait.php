<?php

namespace Tests\Unit\Exercise\Domain\Model\Exercise;

use Exercise\Domain\Models\ExerciseStatus;

trait ExerciseTrait
{
    public static function notAllowedTransitionProvider(): \Generator
    {
        yield 'done_to_skipped' => [ExerciseStatus::DONE, ExerciseStatus::SKIPPED];

        yield 'skipped_to_done' => [ExerciseStatus::SKIPPED, ExerciseStatus::DONE];

        yield 'done_to_in_progress' => [ExerciseStatus::DONE, ExerciseStatus::IN_PROGRESS];

        yield 'done_to_new' => [ExerciseStatus::DONE, ExerciseStatus::NEW];

        yield 'skipped_to_in_progress' => [ExerciseStatus::SKIPPED, ExerciseStatus::IN_PROGRESS];

        yield 'skipped_to_new' => [ExerciseStatus::SKIPPED, ExerciseStatus::NEW];
    }

    public static function statusTransitionProvider(): \Generator
    {
        yield 'new_to_in_progress' => [ExerciseStatus::NEW, ExerciseStatus::IN_PROGRESS];

        yield 'in_progress_to_done' => [ExerciseStatus::IN_PROGRESS, ExerciseStatus::DONE];

        yield 'in_progress_to_skipped' => [ExerciseStatus::IN_PROGRESS, ExerciseStatus::SKIPPED];

        yield 'new_to_done' => [ExerciseStatus::NEW, ExerciseStatus::DONE];

        yield 'new_to_skipped' => [ExerciseStatus::NEW, ExerciseStatus::SKIPPED];
    }
}
