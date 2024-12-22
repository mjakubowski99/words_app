<?php

declare(strict_types=1);

namespace OutboxPattern;

enum OutboxMessageStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
}