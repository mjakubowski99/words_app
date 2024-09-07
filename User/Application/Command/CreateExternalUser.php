<?php

declare(strict_types=1);

namespace User\Application\Command;

use Shared\Enum\UserProvider;

class CreateExternalUser
{
    public function __construct(
        private string $provider_id,
        private UserProvider $provider_type,
        private string $email,
        private string $name,
        private string $picture,
    ) {}

    public function getProviderId(): string
    {
        return $this->provider_id;
    }

    public function getProviderType(): UserProvider
    {
        return $this->provider_type;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }
}
