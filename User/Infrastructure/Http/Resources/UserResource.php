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
    ]
)]
/**
 * @property IUser $resource
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getId()->getValue(),
            'name' => $this->resource->getName(),
            'email' => $this->resource->getEmail(),
        ];
    }
}
