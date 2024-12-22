<?php

namespace OutboxPattern;

interface IProcessResult
{
    public function success(): bool;
    public function getErrors(): ?array;
}