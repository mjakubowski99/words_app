<?php

declare(strict_types=1);

namespace Auth\Domain\Models\DTO;

use UseCases\Contracts\User\ICreateUserRequest;
use UseCases\Contracts\Auth\IRegisterUserRequest;

readonly class CreateUserRequest implements ICreateUserRequest
{
    public function __construct(
        private string $email,
        private string $password,
    ) {}

    public static function fromRegisterRequest(IRegisterUserRequest $request): self
    {
        return new self($request->getEmail(), $request->getPassword());
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
