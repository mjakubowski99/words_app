<?php

namespace OutboxPattern;

class ProcessResult implements IProcessResult
{

    public function success(): bool
    {
        return true;
    }

    public function getErrors(): ?array
    {
        return null;
    }
}