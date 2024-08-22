<?php

namespace Shared\Enum;

enum SessionStatus: string
{
    case STARTED = 'started';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
}
