<?php

declare(strict_types=1);

namespace Shared\Utils\Hash;

use Illuminate\Contracts\Hashing\Hasher;

final class Hash implements IHash
{
    public function __construct(
        private readonly Hasher $hasher
    ) {}

    public function check(string $password, string $password_hash): bool
    {
        return $this->hasher->check($password, $password_hash);
    }

    public function make(string $password): string
    {
        return $this->hasher->make($password);
    }
}
