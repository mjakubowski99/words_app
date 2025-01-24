<?php

declare(strict_types=1);

namespace User\Application\Command;

use Carbon\Carbon;

readonly class CreateUser
{
    public function __construct(
        private string $email,
        private ?Carbon $email_verified_at,
        private string $name,
        private string $password,
        private ?string $picture,
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getEmailVerifiedAt(): ?Carbon
    {
        return $this->email_verified_at;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }
}
