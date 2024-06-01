<?php

declare(strict_types=1);

namespace Shared\Utils\Hash;

interface IHash
{
    public function check(string $password, string $password_hash): bool;

    public function make(string $password): string;
}
