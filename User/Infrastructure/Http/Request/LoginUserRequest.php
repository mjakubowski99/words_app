<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;

#[OAT\Schema(
    schema: 'Requests\User\LoginUserRequest',
    properties: [
        new OAT\Property(
            property: 'username',
            description: 'Username',
            type: 'string',
            example: 'email@email.com'
        ),
        new OAT\Property(
            property: 'password',
            description: 'Password',
            type: 'string',
            example: 'password123'
        ),
    ]
)]
class LoginUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:2', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    public function getUsername(): string
    {
        return $this->input('username');
    }

    public function getPassword(): string
    {
        return $this->input('password');
    }
}
