<?php

declare(strict_types=1);

namespace Auth\Domain\Models\Entities;

use Carbon\Carbon;
use Shared\Enum\UserType;
use Shared\Utils\ValueObjects\Uuid;

interface IPersonalAccessToken
{
    public function getKey(): string;

    public function getUserType(): UserType;

    public function getTokenableId(): Uuid;

    public function getRefreshExpiresAt(): ?Carbon;
}
