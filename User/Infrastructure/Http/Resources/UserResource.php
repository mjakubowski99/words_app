<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Resources;

use Shared\User\IUser;
use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;
use Illuminate\Http\Resources\Json\JsonResource;

#[OAT\Schema(
    schema: 'Resources\User\UserResource',
    properties: [
        new OAT\Property(
            property: 'id',
            type: 'string',
        ),
        new OAT\Property(
            property: 'email',
            type: 'string'
        ),
        new OAT\Property(
            property: 'name',
            type: 'string'
        ),
        new OAT\Property(
            property: 'has_any_session',
            type: 'boolean'
        ),
    ]
)]
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var IUser $user */
        $user = $this->resource['user'];

        /** @var bool $has_any_session */
        $has_any_session = $this->resource['has_any_session'];

        return [
            'id' => $user->getId()->getValue(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'has_any_session' => $has_any_session,
        ];
    }
}
