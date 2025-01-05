<?php

declare(strict_types=1);

namespace User\Domain\Contracts;

use Shared\Enum\ReportType;
use Shared\Enum\ReportableType;
use Shared\Utils\ValueObjects\UserId;

interface ICreateReport
{
    public function getUserId(): ?UserId;

    public function getEmail(): ?string;

    public function getDescription(): string;

    public function getTicketType(): ReportType;

    public function getReportableId(): ?string;

    public function getReportableType(): ?ReportableType;
}
