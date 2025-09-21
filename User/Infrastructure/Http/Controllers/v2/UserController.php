<?php

declare(strict_types=1);

namespace User\Infrastructure\Http\Controllers\v2;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use User\Application\Command\DeleteUserHandler;
use User\Application\Command\CreateReportHandler;
use User\Application\Command\UpdateLanguage;
use User\Infrastructure\Http\Request\DeleteUserRequest;
use User\Infrastructure\Http\Request\StoreReportRequest;
use User\Infrastructure\Http\Request\UpdateLanguageRequest;

class UserController extends Controller
{
    #[OAT\Delete(
        path: '/api/v2/user/me',
        operationId: 'user.me.delete',
        description: 'Delete user account',
        summary: 'Delete user account',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\User\DeleteUserRequest')
        ),
        tags: [Tags::USER, Tags::V2],
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
                'message' => 'Invalid email provided',
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
        path: '/api/v2/reports',
        operationId: 'reports.store',
        description: 'You can use this endpoint to report something to administration. For example you can report flashcard or intent of account deletion',
        summary: 'Store new report',
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\User\StoreReportRequest')
        ),
        tags: [Tags::USER, Tags::V2],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function storeReport(StoreReportRequest $request, CreateReportHandler $handler): JsonResponse
    {
        $handler->handle($request->toCommand());

        return new JsonResponse([], 204);
    }

    #[OAT\Put(
        path: '/api/v2/user/me/language',
        operationId: 'user.me.language.update',
        description: 'Update the user\'s preferred and learning languages',
        summary: 'Update user language preferences',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\User\UpdateLanguageRequest')
        ),
        tags: [Tags::USER, Tags::V2, Tags::LANGUAGE],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
            new OAT\Response(ref: '#/components/responses/server_error', response: 500),
        ],
    )]
    public function updateLanguage(UpdateLanguageRequest $request, UpdateLanguage $handler): JsonResponse
    {
        $handler->handle($request->currentId(), $request->getUserLanguage(), $request->getLearningLanguage());

        return new JsonResponse([], 204);
    }
}
