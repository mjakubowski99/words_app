<?php

declare(strict_types=1);

namespace User\Domain\Models\Entities;

use Shared\Utils\ValueObjects\UserId;

interface IUser
{
    public function getId(): UserId;

    public function getPassword(): string;

    public function getEmail(): string;

    public function getName(): string;
}
