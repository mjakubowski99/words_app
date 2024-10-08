<?php

declare(strict_types=1);

namespace Shared\User;

use Shared\Utils\ValueObjects\UserId;

interface IUser
{
    public function getId(): UserId;

    public function getEmail(): string;

    public function getName(): string;
}
