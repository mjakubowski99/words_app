<?php

declare(strict_types=1);

namespace Shared\Enum;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'ReportableType',
    description: 'Reportable entity type',
    enum: [
        ReportableType::FLASHCARD->value,
    ],
    example: ReportableType::FLASHCARD->value,
    nullable: true
)]
enum ReportableType: string
{
    case FLASHCARD = 'flashcard';
    case UNKNOWN = 'unknown';

    public static function all(): array
    {
        return array_map(fn ($d) => $d->value, self::cases());
    }
}
