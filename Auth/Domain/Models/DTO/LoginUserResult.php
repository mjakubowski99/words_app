<?php

declare(strict_types=1);

namespace Auth\Domain\Models\DTO;

use UseCases\Contracts\Auth\IUserToken;
use UseCases\Contracts\Auth\ILoginUserResult;

class LoginUserResult implements ILoginUserResult
{
    public function __construct(
        private readonly bool $success,
        private readonly ?string $fail_reason,
        private readonly ?IUserToken $token,
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
