<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers\v2;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Illuminate\Http\JsonResponse;
use Flashcard\Application\Query\GetUserFlashcards;
use Flashcard\Application\Query\GetUserRatingStats;
use Flashcard\Application\Command\CreateFlashcardHandler;
use Flashcard\Application\Command\UpdateFlashcardHandler;
use Flashcard\Application\Command\BulkDeleteFlashcardHandler;
use Flashcard\Infrastructure\Http\Request\v2\StoreFlashcardRequest;
use Flashcard\Infrastructure\Http\Resources\v2\RatingStatsResource;
use Flashcard\Infrastructure\Http\Request\v2\UpdateFlashcardRequest;
use Flashcard\Infrastructure\Http\Resources\v2\UserFlashcardsResource;
use Flashcard\Infrastructure\Http\Request\v2\GetFlashcardByUserRequest;
use Flashcard\Infrastructure\Http\Request\v2\BulkDeleteFlashcardRequest;

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

    #[OAT\Delete(
        path: '/api/v2/flashcards/bulk-delete',
        operationId: 'v2.flashcards.bulk-delete',
        description: 'Delete flashcards',
        summary: 'Delete flashcards',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\v2\BulkDeleteFlashcardRequest')
        ),
        tags: [Tags::V2, Tags::FLASHCARD],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function bulkDelete(BulkDeleteFlashcardRequest $request, BulkDeleteFlashcardHandler $handler): JsonResponse
    {
        $handler->handle($request->currentId(), $request->getFlashcardIds());

        return new JsonResponse([], 204);
    }

    #[OAT\Put(
        path: '/api/v2/flashcards/by-user',
        operationId: 'v2.flashcards.get.by-user',
        description: 'Get user flashcards',
        summary: 'Get user flashcards',
        security: [['sanctum' => []]],
        tags: [Tags::V2, Tags::FLASHCARD],
        parameters: [
            new OAT\Parameter(
                name: 'search',
                description: 'Search flashcards parameter',
                in: 'query',
                example: 'Apple',
            ),
            new OAT\Parameter(
                name: 'page',
                description: 'Flashcards page number',
                in: 'query',
                example: 1,
            ),
            new OAT\Parameter(
                name: 'per_page',
                description: 'Flashcards per page',
                in: 'query',
                example: 15,
            ),
        ],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\v2\UserFlashcardsResource'),
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function getByUser(
        GetFlashcardByUserRequest $request,
        GetUserFlashcards $query,
    ): UserFlashcardsResource {
        $flashcards = $query->get(
            $request->currentId(),
            $request->getSearch(),
            $request->getPage(),
            $request->getPerPage()
        );

        return new UserFlashcardsResource($flashcards);
    }

    #[OAT\Get(
        path: '/api/v2/flashcards/by-user/rating-stats',
        operationId: 'v2.flashcards.by-user.rating-stats.get',
        description: 'Get rating stats for user flashcards',
        summary: 'Get rating stats for user flashcards',
        security: [['sanctum' => []]],
        tags: [Tags::V2, Tags::FLASHCARD],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\v2\RatingStatsResource'),
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function userRatingStats(
        Request $request,
        GetUserRatingStats $query,
    ): RatingStatsResource {
        return new RatingStatsResource($query->get($request->currentId()));
    }
}
