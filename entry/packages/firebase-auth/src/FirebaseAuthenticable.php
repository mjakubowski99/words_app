<?php

declare(strict_types=1);

namespace Mjakubowski\FirebaseAuth;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property mixed $id
 */
class FirebaseAuthenticable extends Authenticatable
{
    protected array $claims;

    protected ?string $firebase_token;

    public $incrementing = false;

    protected $guarded = [];

    public function save(array $options = [])
    {
        return true;
    }

    public function resolveByClaims(array $claims): object
    {
        $attributes = $this->transformClaims($claims);
        $attributes['id'] = (string) $claims['sub'];

        return $this->fill($attributes);
    }

    public function updateOrCreateUser(int|string $id, array $attributes): object
    {
        $user = $this->fill($attributes);
        $user->id = $id;

        return $user;
    }

    public function transformClaims(array $claims): array
    {
        $attributes = [];

        $string_keys = ['email', 'name', 'picture'];

        foreach ($string_keys as $key) {
            if (array_key_exists($key, $claims) && $claims[$key]) {
                $attributes[$key] = (string) $claims[$key];
            }
        }

        $attributes['sign_in_provider'] = $claims['firebase']['sign_in_provider'];

        return $attributes;
    }

    public function setFirebaseToken(string $token): self
    {
        $this->firebase_token = $token;

        return $this;
    }

    public function getFirebaseToken(): string
    {
        return $this->firebase_token;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->getProviderId();
    }

    public function getAuthPassword(): string
    {
        throw new \Exception('No password support for Firebase Users');
    }

    public function getRememberToken(): string
    {
        throw new \Exception('No remember token support for Firebase Users');
    }

    public function setRememberToken($value)
    {
        throw new \Exception('No remember token support for Firebase User');
    }

    public function getRememberTokenName()
    {
        throw new \Exception('No remember token support for Firebase User');
    }

    public function getProviderId(): string
    {
        return (string) $this->id;
    }

    public function getEmail(): string
    {
        return $this->attributes['email'];
    }

    public function getName(): string
    {
        return $this->attributes['name'];
    }

    public function getPicture(): string
    {
        return $this->attributes['picture'];
    }

    public function getProviderType(): string
    {
        return $this->attributes['sign_in_provider'];
    }
}
