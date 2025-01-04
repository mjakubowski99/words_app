<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers\v1;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Illuminate\Http\JsonResponse;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Flashcard\Application\Command\CreateFlashcardHandler;
use Flashcard\Application\Command\DeleteFlashcardHandler;
use Flashcard\Application\Command\UpdateFlashcardHandler;
use Flashcard\Infrastructure\Http\Request\v1\StoreFlashcardRequest;
use Flashcard\Infrastructure\Http\Request\v1\UpdateFlashcardRequest;

class FlashcardController
{
    #[OAT\Post(
        path: '/api/flashcards',
        operationId: 'flashcards.store',
        description: 'Create flashcard',
        summary: 'Create flashcard',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\StoreFlashcardRequest')
        ),
        tags: [Tags::FLASHCARD],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function store(
        StoreFlashcardRequest $request,
        CreateFlashcardHandler $create_flashcard_handler
    ): JsonResponse {
        $create_flashcard_handler->handle($request->toCommand());

        return new JsonResponse([], 204);
    }

    #[OAT\Put(
        path: '/api/flashcards/{flashcard_id}',
        operationId: 'flashcards.update',
        description: 'Update flashcard',
        summary: 'Update flashcard',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\UpdateFlashcardRequest')
        ),
        tags: [Tags::FLASHCARD],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function update(
        UpdateFlashcardRequest $request,
        UpdateFlashcardHandler $update_flashcard_handler
    ): JsonResponse {
        $update_flashcard_handler->handle($request->toCommand());

        return new JsonResponse([], 204);
    }

    #[OAT\Delete(
        path: '/api/flashcards/{flashcard_id}',
        operationId: 'flashcards.delete',
        description: 'Delete flashcard',
        summary: 'Delete flashcard',
        security: [['sanctum' => []]],
        tags: [Tags::FLASHCARD],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function delete(
        Request $request,
        DeleteFlashcardHandler $delete_flashcard_handler,
    ): JsonResponse {
        $delete_flashcard_handler->handle(
            $request->currentId(),
            new FlashcardId((int) $request->route('flashcard_id'))
        );

        return new JsonResponse([], 204);
    }
}
