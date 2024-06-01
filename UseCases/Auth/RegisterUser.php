<?php

declare(strict_types=1);

namespace UseCases\Auth;

use UseCases\Contracts\Auth\IRegisterService;
use UseCases\Contracts\Auth\IRegisterUserResult;
use UseCases\Contracts\Auth\IRegisterUserRequest;

readonly class RegisterUser
{
    public function __construct(
        private readonly IRegisterService $service
    ) {}

    public function registerUser(IRegisterUserRequest $request): IRegisterUserResult
    {
        return $this->service->registerUser($request);
    }
}
