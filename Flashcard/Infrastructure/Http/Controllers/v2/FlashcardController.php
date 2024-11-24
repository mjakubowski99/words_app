<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers\v2;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Illuminate\Http\JsonResponse;
use Flashcard\Application\Command\CreateFlashcardHandler;
use Flashcard\Application\Command\UpdateFlashcardHandler;
use Flashcard\Infrastructure\Http\Request\v2\StoreFlashcardRequest;
use Flashcard\Infrastructure\Http\Request\v2\UpdateFlashcardRequest;

class FlashcardController
{
    #[OAT\Post(
        path: '/api/v2/flashcards',
        operationId: 'v2.flashcards.store',
        description: 'Create flashcard',
        summary: 'Create flashcard',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\v2\StoreFlashcardRequest')
        ),
        tags: [Tags::V2, Tags::FLASHCARD],
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
        path: '/api/v2/flashcards/{flashcard_id}',
        operationId: 'v2.flashcards.update',
        description: 'Update flashcard',
        summary: 'Update flashcard',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\v2\UpdateFlashcardRequest')
        ),
        tags: [Tags::V2, Tags::FLASHCARD],
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
}
