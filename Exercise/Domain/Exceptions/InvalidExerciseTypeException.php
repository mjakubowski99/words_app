<?php

declare(strict_types=1);

namespace Exercise\Domain\Exceptions;

use Shared\Exceptions\ApplicationException;

class InvalidExerciseTypeException extends ApplicationException
{
    protected $message = 'Invalid exercise type provided.';
}
