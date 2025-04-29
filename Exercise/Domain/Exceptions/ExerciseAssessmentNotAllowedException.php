<?php

namespace Exercise\Domain\Exceptions;

use Shared\Exceptions\ForbiddenException;

class ExerciseAssessmentNotAllowedException extends ForbiddenException
{
    public function __construct($message = null)
    {
        parent::__construct($message??'Exercise assessment is not allowed');
    }
}