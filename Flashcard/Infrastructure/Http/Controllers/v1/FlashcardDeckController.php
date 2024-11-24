<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers\v1;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Illuminate\Http\JsonResponse;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Application\Query\GetDeckDetails;
use Flashcard\Application\Query\GetUserCategories;
use Flashcard\Application\Command\GenerateFlashcardsHandler;
use Flashcard\Application\Command\MergeFlashcardDecksHandler;
use Flashcard\Infrastructure\Http\Request\v1\GetDeckDetailsRequest;
use Flashcard\Infrastructure\Http\Resources\v1\DeckDetailsResource;
use Flashcard\Infrastructure\Http\Request\v1\MergeFlashcardsRequest;
use Flashcard\Infrastructure\Http\Resources\v1\FlashcardDecksResource;
use Flashcard\Infrastructure\Http\Request\v1\GenerateFlashcardsRequest;
use Flashcard\Infrastructure\Http\Request\v1\IndexFlashcardDeckRequest;
use Flashcard\Application\Command\RegenerateAdditionalFlashcardsHandler;
use Flashcard\Infrastructure\Http\Request\v1\RegenerateFlashcardsRequest;

class FlashcardDeckController
{
    #[OAT\Get(
        path: '/api/flashcards/categories/by-user',
        operationId: 'flashcards.categories.by-user',
        description: 'Get user flashcard categories',
        summary: 'Get user flashcard categories',
        security: [['sanctum' => []]],
        tags: [Tags::FLASHCARD],
        parameters: [
            new OAT\Parameter(
                name: 'search',
                description: 'Search flashcards parameter',
                in: 'query',
                example: 'Apple',
            ),
            new OAT\Parameter(
                name: 'page',
                description: 'Categories page number',
                in: 'query',
                example: 1,
            ),
            new OAT\Parameter(
                name: 'per_page',
                description: 'Categories per page',
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
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\FlashcardCategoriesResource'),
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function index(
        IndexFlashcardDeckRequest $request,
        GetUserCategories $get_user_decks,
    ): FlashcardDecksResource {
        return new FlashcardDecksResource([
            'decks' => $get_user_decks->handle(
                new Owner(new OwnerId($request->getUserId()->getValue()), FlashcardOwnerType::USER),
                $request->getSearch(),
                $request->getPage(),
                $request->getPerPage(),
            ),
            'page' => $request->getPage(),
            'per_page' => $request->getPerPage(),
        ]);
    }

    #[OAT\Post(
        path: '/api/flashcards/categories/generate-flashcards',
        operationId: 'flashcards.generate-by-category',
        description: 'Generate flashcards by provided category',
        summary: 'Generate flashcards by provided category',
        security: [['sanctum' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\GenerateFlashcardsRequest')
        ),
        tags: [Tags::FLASHCARD],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\CategoryDetailsResource'),
                    ),
                    new OAT\Property(
                        property: 'merged_to_existing_category',
                        description: "Variable determinate if generated flashcards are merged to existing category or it's brand new category",
                        type: 'bool',
                        example: false
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function generateFlashcards(
        GenerateFlashcardsRequest $request,
        GenerateFlashcardsHandler $generate_flashcards,
        GetDeckDetails $get_deck_details,
    ): DeckDetailsResource {
        $result = $generate_flashcards->handle($request->toCommand());

        return (new DeckDetailsResource([
            'details' => $get_deck_details->get($result->getDeckId(), null, $request->getPage(), $request->getPerPage()),
            'page' => $request->getPage(),
            'per_page' => $request->getPerPage(),
        ]))->additional([
            'merged_to_existing_category' => $result->getMergedToExistingDeck(),
        ]);
    }

    #[OAT\Post(
        path: '/api/flashcards/categories/{category_id}/generate-flashcards',
        operationId: 'flashcards.categories.category.generate-flashcards',
        description: 'Generate flashcards for existing category',
        summary: 'Generate flashcards for provided category',
        security: [['sanctum' => []]],
        tags: [Tags::FLASHCARD],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'success',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(
                        property: 'data',
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\CategoryDetailsResource'),
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function regenerateFlashcards(
        RegenerateFlashcardsRequest $request,
        RegenerateAdditionalFlashcardsHandler $regenerate_flashcards,
        GetDeckDetails $get_deck_details,
    ): DeckDetailsResource {
        $regenerate_flashcards->handle($request->getOwner(), $request->getDeckId());

        return new DeckDetailsResource([
            'details' => $get_deck_details->get($request->getDeckId(), null, $request->getPage(), $request->getPerPage()),
            'page' => $request->getPage(),
            'per_page' => $request->getPerPage(),
        ]);
    }

    #[OAT\Post(
        path: '/api/flashcards/categories/{from_category_id}/merge-flashcards/{to_category_id}',
        operationId: 'flashcards.categories.merge-flashcards',
        description: 'Merge flashcards',
        summary: 'Merge flashcards',
        security: [['sanctum' => []]],
        tags: [Tags::FLASHCARD],
        parameters: [
            new OAT\Parameter(
                name: 'from_category_id',
                description: 'Category which will be merged and deleted',
                in: 'path',
                example: '1',
            ),
            new OAT\Parameter(
                name: 'to_category_id',
                description: 'Category to which flashcards from from_category will be merged',
                in: 'path',
                example: '2',
            ),
        ],
        responses: [
            new OAT\Response(ref: '#/components/responses/no_content', response: 204),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function merge(
        MergeFlashcardsRequest $request,
        MergeFlashcardDecksHandler $merge_handler
    ): JsonResponse {
        $merge_handler->handle(
            $request->getOwner(),
            $request->getFromDeckId(),
            $request->getToDeckId(),
            $request->getNewName()
        );

        return new JsonResponse([], 204);
    }

    #[OAT\Get(
        path: '/api/flashcards/categories/{category_id}',
        operationId: 'flashcards.categories.get',
        description: 'Get flashcard details for category',
        summary: 'Get flashcards and category details',
        security: [['sanctum' => []]],
        tags: [Tags::FLASHCARD],
        parameters: [
            new OAT\Parameter(
                name: 'search',
                description: 'Search flashcards parameter',
                in: 'query',
                example: 'Apple',
            ),
            new OAT\Parameter(
                name: 'page',
                description: 'Categories page number',
                in: 'query',
                example: 1,
            ),
            new OAT\Parameter(
                name: 'per_page',
                description: 'Categories per page',
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
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\CategoryDetailsResource'),
                    ),
                ]),
            ),
            new OAT\Response(ref: '#/components/responses/bad_request', response: 400),
            new OAT\Response(ref: '#/components/responses/unauthenticated', response: 401),
            new OAT\Response(ref: '#/components/responses/validation_error', response: 422),
        ],
    )]
    public function get(
        GetDeckDetailsRequest $request,
        GetDeckDetails $get_deck_details,
    ): DeckDetailsResource {
        return new DeckDetailsResource([
            'details' => $get_deck_details->get(
                $request->getDeckId(),
                $request->getSearch(),
                $request->getPage(),
                $request->getPerPage()
            ),
            'page' => $request->getPage(),
            'per_page' => $request->getPerPage(),
        ]);
    }
}
