<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Controllers;

use UseCases\Auth\Create;
use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use App\Http\Controllers\Controller;
use User\Infrastructure\Http\Request\GetUserRequest;
use User\Infrastructure\Http\Resources\UserResource;

class UserController extends Controller
{
    #[OAT\Get(
        path: '/api/user/me',
        operationId: 'user.me',
        description: 'User me',
        summary: 'User me',
        security: [['firebase' => []]],
        tags: [Tags::USER],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\User\UserResource'),
                    ),
                ]),
            ),
            new OAT\Response(
                response: 401,
                description: 'bad request',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Unauthenticated'
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function me(GetUserRequest $request, Create $create): UserResource
    {
        $user = $create->findOrCreate($request->authenticable());

        return new UserResource($user);
    }
}
