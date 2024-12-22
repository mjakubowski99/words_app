<?php

namespace OutboxPattern;

class Publisher implements IPublisher
{

    public function publish(OutboxMessageType $type, array $payload = []): void
    {
        OutboxMessage::query()->create([
            'type' => $type->value,
            'status' => OutboxMessageStatus::PENDING->value,
            'payload' => $payload,
            'processed_at' => null,
            'retry_after' => null,
            'locked_until' => null,
            'attempts' => 0,
            'errors' => null,
        ]);
    }
}