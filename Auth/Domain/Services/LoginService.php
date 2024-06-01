<?php

declare(strict_types=1);

namespace Auth\Domain\Services;

use UseCases\Contracts\User\IUser;
use Auth\Domain\Models\DTO\UserToken;
use Auth\Domain\Models\Enum\FailReason;
use UseCases\Contracts\Auth\IUserToken;
use UseCases\Contracts\User\IUserService;
use UseCases\Contracts\Auth\ILoginService;
use Auth\Domain\Models\DTO\LoginUserResult;
use UseCases\Contracts\Auth\ILoginUserResult;
use UseCases\Contracts\Auth\IUserLoginRequest;

readonly class LoginService implements ILoginService
{
    public function __construct(
        private IUserService $service,
        private TokenService $token_service,
    ) {}

    public function loginByCredentials(IUserLoginRequest $request): ILoginUserResult
    {
        $user = $this->service->validateCredentials($request->getEmail(), $request->getPassword());

        if (!$user) {
            return new LoginUserResult(false, FailReason::INVALID_LOGIN_CREDENTIALS->value, null);
        }

        return new LoginUserResult(true, null, $this->loginUser($user));
    }

    public function loginUser(IUser $user): IUserToken
    {
        $token = $this->token_service->createUserToken($user);

        return new UserToken($user, $token);
    }
}
