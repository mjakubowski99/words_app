<?php

declare(strict_types=1);

namespace Shared\Enum;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'TicketType',
    description: 'Type of ticket',
    enum: [
        TicketType::DELETE_ACCOUNT->value,
        TicketType::INAPPROPRIATE_CONTENT->value,
    ],
    example: TicketType::INAPPROPRIATE_CONTENT->value,
    nullable: true
)]
enum TicketType: string
{
    case DELETE_ACCOUNT = 'delete_account';
    case INAPPROPRIATE_CONTENT = 'inappropriate_content';

    public static function all(): array
    {
        return array_map(fn($d) => $d->value, self::cases());
    }
}