<?php

namespace Flashcard\Domain\Contracts;

use Shared\Utils\ValueObjects\UserId;

interface IExerciseFactoryContext
{
    public function getUserId(): UserId;
}