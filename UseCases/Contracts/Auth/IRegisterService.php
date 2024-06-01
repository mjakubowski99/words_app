<?php

declare(strict_types=1);

namespace UseCases\Contracts\Auth;

interface IRegisterService
{
    public function registerUser(IRegisterUserRequest $request): IRegisterUserResult;
}
