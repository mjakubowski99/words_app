<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\OpenApi\Tags;
use UseCases\Auth\LoginUser;
use OpenApi\Attributes as OAT;
use UseCases\Auth\RegisterUser;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserTokenResource;

class AuthController extends Controller
{
    #[OAT\Post(
        path: '/api/login',
        operationId: 'auth.login',
        description: 'Login user',
        summary: 'Login user',
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Auth\LoginRequest')
        ),
        tags: ['Auth'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Auth\UserTokenResource'),
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
                        example: 'Invalid login credentials'
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function loginUser(LoginRequest $request, LoginUser $use_case): JsonResponse|UserTokenResource
    {
        $result = $use_case->login($request);

        if (!$result->success()) {
            return new JsonResponse(['message' => $result->getFailReason()], 400);
        }

        return new UserTokenResource($result->getUserToken());
    }

    #[OAT\Post(
        path: '/api/register',
        operationId: 'auth.register',
        description: 'Register user',
        summary: 'Register user',
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Auth\RegisterRequest')
        ),
        tags: [Tags::AUTH],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Auth\UserTokenResource'),
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
                        example: 'Invalid login credentials'
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function registerUser(RegisterRequest $request, RegisterUser $use_case): JsonResponse|UserTokenResource
    {
        $result = $use_case->registerUser($request);

        if (!$result->success()) {
            return new JsonResponse(['message' => $result->getFailReason()], 400);
        }

        return new UserTokenResource($result->getUserToken());
    }
}
