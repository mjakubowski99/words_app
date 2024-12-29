<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Controllers;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use User\Application\Command\CreateTicketHandler;
use User\Application\Command\DeleteUserHandler;
use User\Application\Query\GetOAuthUser;
use User\Application\Command\CreateExternalUser;
use User\Application\Command\CreateTokenHandler;
use User\Application\Query\FindExternalUserHandler;
use User\Infrastructure\Http\Request\DeleteUserRequest;
use User\Infrastructure\Http\Request\GetUserRequest;
use User\Infrastructure\Http\Request\StoreTicketRequest;
use User\Infrastructure\Http\Resources\UserResource;
use User\Application\Command\CreateExternalUserHandler;
use User\Infrastructure\Http\Request\OAuthLoginRequest;

class UserController extends Controller
{
    #[OAT\Post(
        path: '/api/user/oauth/login',
        operationId: 'user.oauth.login',
        description: 'Endpoint should be called after completing login with oauth service like google to init user on backend side',
        summary: 'Exchange oauth token for app token and init user if needed',
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\User\OAuthLoginRequest')
        ),
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
                                type: 'string',
                                example: '123',
                            ),
                            new OAT\Property(
                                property: 'data',
                                type: 'array',
                                items: new OAT\Items(ref: '#/components/schemas/Resources\User\UserResource'),
                            ),
                        ],
                        type: 'object',
                    ),
                ]),
            ),
            new OAT\Response(
                response: 400,
                description: 'bad request',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Some message'
                    ),
                ]),
            ),
        ],
    )]
    public function loginWithProvider(
        OAuthLoginRequest $request,
        GetOAuthUser $get_oauth_user,
        CreateExternalUserHandler $create,
        FindExternalUserHandler $find,
        CreateTokenHandler $create_token,
    ): JsonResponse {
        $oauth_user = $get_oauth_user->get(
            $request->getUserProvider(),
            $request->getAccessToken(),
            $request->getPlatform()
        );

        $command = new CreateExternalUser(
            $oauth_user->getId(),
            $oauth_user->getUserProvider(),
            $oauth_user->getEmail(),
            $oauth_user->getName() ?? '',
            $oauth_user->getAvatar() ?? ''
        );

        $create->handle($command);

        $user = $find->handle($command->getProviderId(), $command->getProviderType());

        return new JsonResponse([
            'data' => [
                'token' => $create_token->handle($user->getId()),
                'user' => new UserResource($user),
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

    #[OAT\Delete(
        path: '/api/user/me',
        operationId: 'user.me.delete',
        description: 'Delete user account',
        summary: 'Delete user account',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\User\DeleteUserRequest')
        ),
        tags: [Tags::USER],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
            new OAT\Response(ref: '#/components/responses/server_error', response: 500),
        ],
    )]
    public function delete(DeleteUserRequest $request, DeleteUserHandler $handler): JsonResponse
    {
        if ($request->current()->getEmail() !== $request->getEmail()) {
            return new JsonResponse([
                'message' => 'Invalid email provided'
            ], 400);
        }

        $result = $handler->delete($request->currentId());
        
        if (!$result) {
            return new JsonResponse([
                'message' => 'Something went wrong',
            ], 500);
        }

        return new JsonResponse([], 204);
    }

    #[OAT\Post(
        path: '/api/tickets',
        operationId: 'tickets.store',
        description: 'You can use this endpoint to report something to administration. For example you can report flashcard or intent of account deletion',
        summary: 'Store new ticket',
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\User\StoreTicketRequest')
        ),
        tags: [Tags::USER],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function storeTicket(StoreTicketRequest $request, CreateTicketHandler $handler): JsonResponse
    {
        $handler->handle($request->toCommand());

        return new JsonResponse([], 204);
    }
}
