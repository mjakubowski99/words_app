<?php

declare(strict_types=1);

namespace Exercise\Domain\Models;

enum ExerciseStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case SKIPPED = 'skipped';

    public function isDone(): bool
    {
        return $this->value === self::DONE->value;
    }
}
