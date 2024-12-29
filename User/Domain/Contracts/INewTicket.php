<?php

declare(strict_types=1);

namespace User\Domain\Contracts;

use Shared\Enum\ReportableType;
use Shared\Enum\TicketType;
use Shared\Utils\ValueObjects\UserId;

interface INewTicket
{
    public function getUserId(): ?UserId;
    public function getEmail(): ?string;

    public function getDescription(): string;

    public function getTicketType(): TicketType;

    public function getReportableId(): ?string;
}