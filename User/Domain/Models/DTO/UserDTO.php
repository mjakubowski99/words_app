<?php

declare(strict_types=1);

namespace User\Domain\Models\DTO;

use UseCases\Contracts\User\IUser;
use Shared\Utils\ValueObjects\Uuid;

readonly class UserDTO implements IUser
{
    public function __construct(
        private \User\Domain\Models\Entities\IUser $domain_user
    ) {}

    public function getId(): Uuid
    {
        return $this->domain_user->getId();
    }

    public function getPassword(): string
    {
        return $this->domain_user->getPassword();
    }

    public function getEmail(): string
    {
        return $this->domain_user->getEmail();
    }

    public function getName(): string
    {
        return $this->domain_user->getName();
    }
}
