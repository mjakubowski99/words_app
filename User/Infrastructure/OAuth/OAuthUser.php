<?php

declare(strict_types=1);

namespace User\Infrastructure\OAuth;

use Shared\Enum\UserProvider;
use User\Domain\Contracts\IOAuthUser;

class OAuthUser implements IOAuthUser
{
    public function __construct(
        private string $id,
        private UserProvider $user_provider,
        private ?string $name,
        private string $email,
        private ?string $nickname,
        private ?string $avatar,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserProvider(): UserProvider
    {
        return $this->user_provider;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }
}
