<?php

namespace Exercise\Domain\Exceptions;

use Exercise\Domain\Models\ExerciseStatus;
use Shared\Exceptions\ForbiddenException;

class ExerciseStatusTransitionException extends ForbiddenException
{
    public function __construct(ExerciseStatus $current_status, ExerciseStatus $new_status)
    {
        $message = sprintf(
            'Transition from exercise status %s to %s is not allowed',
            $current_status->value,
            $new_status->value
        );

        parent::__construct($message);
    }
}