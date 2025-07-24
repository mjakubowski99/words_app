<?php

namespace Exercise\Domain\Exceptions;

use Shared\Exceptions\ApplicationException;

class InvalidExerciseTypeException extends ApplicationException
{
    protected $message = 'Invalid exercise type provided.';
}