<?php

namespace OutboxPattern;

use Carbon\Carbon;

interface IOutboxMessage
{
    public function getId(): int;
    public function getType(): OutboxMessageType;
    public function getStatus(): OutboxMessageStatus;
    public function getPayload(): array;
    public function getCreatedAt(): Carbon;
}