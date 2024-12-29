<?php

namespace User\Infrastructure\Http\Request;

use Shared\Http\Request\Request;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    schema: 'Requests\User\DeleteUserRequest',
    properties: [
        new OAT\Property(
            property: 'email',
            description: "Email associated with account to confirm account deletion",
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
            'email' => ['required', 'string', 'email']
        ];
    }

    public function getEmail(): string
    {
        return $this->input('email');
    }
}