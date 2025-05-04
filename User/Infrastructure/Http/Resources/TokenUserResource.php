<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Resources;

use Shared\User\IUser;
use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;

#[OAT\Schema(
    schema: 'Resources\User\v2\TokenUserResource',
    properties: [
        new OAT\Property(
            property: 'token',
            type: 'string',
            example: '123',
        ),
        new OAT\Property(
            property: 'data',
            type: 'array',
            items: new OAT\Items(ref: '#/components/schemas/Resources\User\UserResource'),
        ),
    ]
)]
class TokenUserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        /** @var string $token */
        $token = $this->resource['token'];

        /** @var IUser $user */
        $user = $this->resource['user'];

        /** @var bool $has_any_session */
        $has_any_session = $this->resource['has_any_session'];

        return [
            'token' => $token,
            'user' => new UserResource([
                'user' => $user,
                'has_any_session' => $has_any_session,
            ]),
        ];
    }
}
