<?php

declare(strict_types=1);

namespace User\Domain\Models\Entities;

use Shared\Utils\ValueObjects\Uuid;

interface IUser
{
    public function getId(): Uuid;

    public function getPassword(): string;

    public function getEmail(): string;
}
