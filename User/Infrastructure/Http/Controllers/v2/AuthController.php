<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Controllers\v2;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Shared\Flashcard\IFlashcardFacade;
use Shared\Exceptions\BadRequestException;
use User\Application\Command\LoginUserHandler;
use User\Application\Command\CreateTokenHandler;
use User\Infrastructure\Http\Request\LoginUserRequest;
use User\Infrastructure\Http\Resources\TokenUserResource;

class AuthController
{
    #[OAT\Post(
        path: '/api/v2/user/login',
        operationId: 'user.login',
        description: 'Endpoint for user login',
        summary: 'Endpoint for user login',
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\User\LoginUserRequest')
        ),
        tags: [Tags::USER],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\User\v2\TokenUserResource'),
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
            new OAT\Response(
                response: 429,
                description: 'too many requests',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Too many requests'
                    ),
                ]),
            ),
        ],
    )]
    public function login(
        LoginUserRequest $request,
        LoginUserHandler $login_handler,
        CreateTokenHandler $token_handler,
        IFlashcardFacade $flashcard_facade,
    ): TokenUserResource {
        $user = $login_handler->handle($request->getUsername(), $request->getPassword());

        if (!$user) {
            throw new BadRequestException('Invalid username or password');
        }

        return new TokenUserResource([
            'token' => $token_handler->handle($user->getId()),
            'user' => $user,
            'has_any_session' => $flashcard_facade->hasAnySession($user->getId()),
        ]);
    }
}
