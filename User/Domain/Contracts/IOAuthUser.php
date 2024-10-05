<?php

declare(strict_types=1);

namespace User\Domain\Contracts;

use Shared\Enum\UserProvider;

interface IOAuthUser
{
    public function getId(): string;

    public function getUserProvider(): UserProvider;

    public function getName(): ?string;

    public function getEmail(): string;

    public function getNickname(): ?string;

    public function getAvatar(): ?string;
}
