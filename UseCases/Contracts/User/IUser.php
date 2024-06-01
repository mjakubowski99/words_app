<?php

declare(strict_types=1);

namespace UseCases\Contracts\User;

use Shared\Utils\ValueObjects\Uuid;

interface IUser
{
    public function getId(): Uuid;

    public function getEmail(): string;
}
