<?php

declare(strict_types=1);

namespace Auth\Domain\Services;

use UseCases\Contracts\User\IUserService;
use UseCases\Contracts\Auth\ILoginService;
use Auth\Domain\Models\DTO\CreateUserRequest;
use UseCases\Contracts\Auth\IRegisterService;
use Auth\Domain\Models\DTO\RegisterUserResult;
use UseCases\Contracts\Auth\IRegisterUserResult;
use UseCases\Contracts\Auth\IRegisterUserRequest;

readonly class RegisterService implements IRegisterService
{
    public function __construct(
        private IUserService $user_service,
        private ILoginService $login_service,
    ) {}

    public function registerUser(IRegisterUserRequest $request): IRegisterUserResult
    {
        $request = CreateUserRequest::fromRegisterRequest($request);

        $user = $this->user_service->createUser($request);

        $token = $this->login_service->loginUser($user);

        return new RegisterUserResult(true, null, $token);
    }
}
