<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use OpenApi\Attributes as OAT;
use Illuminate\Foundation\Http\FormRequest;
use UseCases\Contracts\Auth\IUserLoginRequest;

#[OAT\Schema(
    schema: 'Requests\Auth\LoginRequest',
    properties: [
        new OAT\Property(
            property: 'email',
            description: 'Email.',
            type: 'string',
            example: 'email@email.com',
        ),
        new OAT\Property(
            property: 'password',
            description: 'Password.',
            type: 'string',
            format: 'password',
            example: 'test123'
        ),
    ]
)]
class LoginRequest extends FormRequest implements IUserLoginRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required'],
            'password' => ['required'],
        ];
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }

    public function getPassword(): string
    {
        return $this->input('password');
    }
}
