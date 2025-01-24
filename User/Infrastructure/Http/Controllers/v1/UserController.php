<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Controllers\v1;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use App\Http\Controllers\Controller;
use Shared\Flashcard\IFlashcardFacade;
use User\Application\Query\GetOAuthUser;
use User\Application\Command\CreateExternalUser;
use User\Application\Command\CreateTokenHandler;
use User\Application\Query\FindExternalUserHandler;
use User\Infrastructure\Http\Request\GetUserRequest;
use User\Infrastructure\Http\Resources\UserResource;
use User\Application\Command\CreateExternalUserHandler;
use User\Infrastructure\Http\Request\OAuthLoginRequest;
use User\Infrastructure\Http\Resources\TokenUserResource;

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
        IFlashcardFacade $flashcard_facade,
    ): TokenUserResource {
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

        return new TokenUserResource([
            'token' => $create_token->handle($user->getId()),
            'user' => $user,
            'has_any_session' => $flashcard_facade->hasAnySession($user->getId()),
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
    public function me(GetUserRequest $request, IFlashcardFacade $flashcard_facade): UserResource
    {
        return new UserResource([
            'user' => $request->current(),
            'has_any_session' => $flashcard_facade->hasAnySession($request->currentId()),
        ]);
    }
}
