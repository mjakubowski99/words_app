<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Request;

use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;

#[OAT\Schema(
    schema: 'Requests\User\DeleteUserRequest',
    properties: [
        new OAT\Property(
            property: 'email',
            description: 'Email associated with account to confirm account deletion',
            type: 'string',
            example: 'email@email.com',
            nullable: true,
        ),
    ]
)]
class DeleteUserRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }
}
