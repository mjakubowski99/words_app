<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use OpenApi\Attributes as OAT;
use UseCases\Contracts\Auth\IUserToken;
use Illuminate\Http\Resources\Json\JsonResource;

#[OAT\Schema(
    schema: 'Resources\Auth\UserTokenResource',
    properties: [
        new OAT\Property(
            property: 'user',
            properties: [
                new OAT\Property(
                    property: 'id',
                    type: 'string',
                ),
                new OAT\Property(
                    property: 'name',
                    type: 'string'
                ),
            ]
        ),
        new OAT\Property(
            property: 'token',
            type: 'string'
        ),
    ]
)]
/**
 * @property IUserToken $resource
 */
class UserTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => (string) $this->resource->getUser()->getId(),
                'name' => $this->resource->getUser()->getEmail(),
            ],
            'token' => $this->resource->getToken(),
        ];
    }
}
