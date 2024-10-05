<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Request;

use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use OpenApi\Attributes as OAT;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

#[OAT\Schema(
    schema: 'Requests\User\OAuthLoginRequest',
    properties: [
        new OAT\Property(
            property: 'access_token',
            description: 'External oauth service access token',
            type: 'string',
            example: 'eYjjwwawwaadasda'
        ),
        new OAT\Property(
            property: 'user_provider',
            type: 'string',
            enum: [UserProvider::GOOGLE->value],
            example: UserProvider::GOOGLE->value
        ),
    ]
)]
class OAuthLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'access_token' => ['required', 'string'],
            'user_provider' => ['required', Rule::in([UserProvider::GOOGLE->value])],
            'platform' => ['required', Rule::in([Platform::WEB->value, Platform::ANDROID->value])],
        ];
    }

    public function getAccessToken(): string
    {
        return $this->input('access_token');
    }

    public function getUserProvider(): UserProvider
    {
        return UserProvider::from($this->input('user_provider'));
    }

    public function getPlatform(): Platform
    {
        return Platform::from($this->input('platform'));
    }
}
