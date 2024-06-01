<?php

declare(strict_types=1);

namespace UseCases\Contracts\Auth;

interface ILoginUserResult
{
    public function success(): bool;

    public function getFailReason(): string;

    public function getUserToken(): IUserToken;
}
