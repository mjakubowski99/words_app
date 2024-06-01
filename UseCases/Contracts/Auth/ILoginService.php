<?php

declare(strict_types=1);

namespace UseCases\Contracts\Auth;

use UseCases\Contracts\User\IUser;

interface ILoginService
{
    public function loginByCredentials(IUserLoginRequest $request): ILoginUserResult;

    public function loginUser(IUser $user): IUserToken;
}
