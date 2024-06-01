<?php

declare(strict_types=1);

namespace Auth\Domain\Models\DTO;

use UseCases\Contracts\Auth\IUserToken;
use UseCases\Contracts\Auth\IRegisterUserResult;

readonly class RegisterUserResult implements IRegisterUserResult
{
    public function __construct(
        private bool $success,
        private ?string $fail_reason,
        private ?IUserToken $token,
    ) {}

    public function success(): bool
    {
        return $this->success;
    }

    public function getFailReason(): string
    {
        return $this->fail_reason;
    }

    public function getUserToken(): IUserToken
    {
        return $this->token;
    }
}
