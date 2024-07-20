<?php

declare(strict_types=1);

namespace UseCases\Contracts\Auth;

use Shared\Enum\UserProvider;

interface IAuthenticable
{
    public function getGuard(): string;

    public function getProviderId(): string;

    public function getProviderType(): UserProvider;

    public function getEmail(): string;

    public function getName(): string;

    public function getPicture(): string;
}
