<?php

namespace OutboxPattern;

use Illuminate\Support\Facades\Log;

class LogProcessor implements IProcessor
{
    public function process(IOutboxMessage $message): IProcessResult
    {
        Log::info("Message processed with id: " . $message->getId());

        return new ProcessResult();
    }

    public function getBackoff(): ?array
    {
        return [];
    }

    public function getMaxAttempts(): ?int
    {
        return 1;
    }
}