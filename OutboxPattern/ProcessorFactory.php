<?php

declare(strict_types=1);

namespace OutboxPattern;

class ProcessorFactory
{
    public function make(OutboxMessageType $type): IProcessor
    {
        return new LogProcessor();
    }
}