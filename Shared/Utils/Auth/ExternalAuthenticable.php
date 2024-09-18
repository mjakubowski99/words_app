<?php

declare(strict_types=1);

namespace Shared\Utils\Auth;

use Shared\Enum\UserProvider;
use Mjakubowski\FirebaseAuth\FirebaseAuthenticable;

class ExternalAuthenticable
{
    public function __construct(
        private readonly string $provider_id,
        private readonly UserProvider $provider,
        private readonly string $email,
        private readonly string $name,
        private readonly string $picture,
        private readonly string $guard,
    ) {}

    public static function resolveUserProvider(string $provider): UserProvider
    {
        return match ($provider) {
            'google.com' => UserProvider::GOOGLE,
            default => throw new \UnexpectedValueException('Unknown provider'),
        };
    }

    public static function fromFirebase(FirebaseAuthenticable $authenticable): self
    {
        return new self(
            $authenticable->getProviderId(),
            self::resolveUserProvider($authenticable->getProviderType()),
            $authenticable->getEmail(),
            $authenticable->getName(),
            $authenticable->getPicture(),
            'firebase'
        );
    }

    public function getProviderId(): string
    {
        return $this->provider_id;
    }

    public function getProviderType(): UserProvider
    {
        return $this->provider;
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

    public function getGuard(): string
    {
        return $this->guard;
    }
}
