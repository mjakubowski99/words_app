<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use OpenApi\Attributes as OAT;
use Illuminate\Foundation\Http\FormRequest;
use UseCases\Contracts\Auth\IRegisterUserRequest;

#[OAT\Schema(
    schema: 'Requests\Auth\RegisterRequest',
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
class RegisterRequest extends FormRequest implements IRegisterUserRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ];
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }

    public function getUserPassword(): string
    {
        return $this->input('password');
    }
}
