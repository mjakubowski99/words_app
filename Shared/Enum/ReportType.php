<?php

declare(strict_types=1);

namespace Shared\Enum;

use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'ReportType',
    description: 'Type of report',
    enum: [
        ReportType::DELETE_ACCOUNT->value,
        ReportType::INAPPROPRIATE_CONTENT->value,
    ],
    example: ReportType::INAPPROPRIATE_CONTENT->value,
    nullable: true
)]
enum ReportType: string
{
    case DELETE_ACCOUNT = 'delete_account';
    case INAPPROPRIATE_CONTENT = 'inappropriate_content';

    public static function all(): array
    {
        return array_map(fn ($d) => $d->value, self::cases());
    }
}
