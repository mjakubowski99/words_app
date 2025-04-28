<?php

namespace Exercise\Domain\Models;

enum ExerciseStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case SKIPPED = 'skipped';
}