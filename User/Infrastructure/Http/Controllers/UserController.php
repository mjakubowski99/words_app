<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Controllers;

use UseCases\User\Create;
use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use App\Http\Controllers\Controller;
use Shared\Auth\ExternalAuthenticable;
use User\Infrastructure\Http\Request\GetUserRequest;
use User\Infrastructure\Http\Resources\UserResource;

class UserController extends Controller
{
    #[OAT\Get(
        path: '/api/user/firebase-init',
        operationId: 'user.firebase-init',
        description: 'Firebase user',
        summary: 'Create firebase user if not exists',
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
        ],
    )]
    public function initFirebaseUser(Request $request, Create $use_case): UserResource
    {
        $firebase_authenticable = ExternalAuthenticable::fromFirebase(
            $request->user('firebase')
        );

        return new UserResource(
            $use_case->createByExternal($firebase_authenticable)
        );
    }

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
        ],
    )]
    public function me(GetUserRequest $request): UserResource
    {
        return new UserResource($request->current());
    }
}
