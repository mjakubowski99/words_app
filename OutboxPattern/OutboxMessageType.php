<?php

namespace OutboxPattern;

enum OutboxMessageType: string
{
    case DEFAULT = 'default';
}