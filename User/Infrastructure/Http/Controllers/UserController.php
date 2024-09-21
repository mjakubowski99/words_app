<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Controllers;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Shared\Utils\Auth\ExternalAuthenticable;
use User\Application\Command\CreateExternalUser;
use User\Application\Command\CreateTokenHandler;
use User\Application\Query\FindExternalUserHandler;
use User\Infrastructure\Http\Request\GetUserRequest;
use User\Infrastructure\Http\Resources\UserResource;
use User\Application\Command\CreateExternalUserHandler;

class UserController extends Controller
{
    #[OAT\Post(
        path: '/api/user/firebase-init',
        operationId: 'user.firebase-init',
        description: 'Endpoint should be called after completing login with firebase to init user on backend side',
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
                        properties: [
                            new OAT\Property(
                                property: 'token',
                                example: '123',
                                type: 'string'
                            ),
                            new OAT\Property(
                                property: 'data',
                                type: 'array',
                                items: new OAT\Items(ref: '#/components/schemas/Resources\User\UserResource'),
                            ),
                        ],
                        type: 'array'
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
    public function initFirebaseUser(
        Request $request,
        CreateExternalUserHandler $create,
        FindExternalUserHandler $find,
        CreateTokenHandler $create_token,
    ): JsonResponse {
        $firebase_authenticable = ExternalAuthenticable::fromFirebase($request->user('firebase'));

        $command = new CreateExternalUser(
            $firebase_authenticable->getProviderId(),
            $firebase_authenticable->getProviderType(),
            $firebase_authenticable->getEmail(),
            $firebase_authenticable->getName(),
            $firebase_authenticable->getPicture()
        );

        $create->handle($command);

        $user = $find->handle(
            $firebase_authenticable->getProviderId(),
            $firebase_authenticable->getProviderType()
        );

        return new JsonResponse([
            'data' => [
                'token' => $create_token->handle($user->getId()),
                'user' => new UserResource(
                    $find->handle(
                        $firebase_authenticable->getProviderId(),
                        $firebase_authenticable->getProviderType()
                    )
                ),
            ],
        ]);
    }

    #[OAT\Get(
        path: '/api/user/me',
        operationId: 'user.me',
        description: 'User me',
        summary: 'User me',
        security: [['sanctum' => []]],
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
