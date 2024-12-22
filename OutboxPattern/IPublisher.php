<?php

namespace OutboxPattern;

interface IPublisher
{
    public function publish(OutboxMessageType $type, array $payload = []);
}