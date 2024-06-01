<?php

declare(strict_types=1);

namespace UseCases\Auth;

use UseCases\Contracts\Auth\ILoginService;
use UseCases\Contracts\Auth\ILoginUserResult;
use UseCases\Contracts\Auth\IUserLoginRequest;

readonly class LoginUser
{
    public function __construct(
        private ILoginService $login_service,
    ) {}

    public function login(IUserLoginRequest $request): ILoginUserResult
    {
        return $this->login_service->loginByCredentials($request);
    }
}
