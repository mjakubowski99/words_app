<?php

namespace OutboxPattern;

interface IProcessor
{
    public function process(IOutboxMessage $message): IProcessResult;

    public function getBackoff(): ?array;
    public function getMaxAttempts(): ?int;
}