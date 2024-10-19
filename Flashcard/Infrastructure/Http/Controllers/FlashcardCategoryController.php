<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Shared\Http\Request\Request;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
use Flashcard\Domain\ValueObjects\CategoryId;
use Flashcard\Application\Query\GetMainCategory;
use Flashcard\Application\Query\GetUserCategories;
use Flashcard\Application\Query\GetCategoryDetails;
use Flashcard\Application\Command\GenerateFlashcardsHandler;
use Flashcard\Infrastructure\Http\Request\GenerateFlashcardsRequest;
use Flashcard\Infrastructure\Http\Resources\CategoryDetailsResource;
use Flashcard\Infrastructure\Http\Request\IndexFlashcardCategoryRequest;
use Flashcard\Infrastructure\Http\Resources\FlashcardCategoriesResource;

class FlashcardCategoryController
{
    #[OAT\Get(
        path: '/api/flashcards/categories/by-user',
        operationId: 'flashcards.categories.index',
        description: 'Get user flashcard categories',
        summary: 'Get user flashcard categories',
        security: [['sanctum' => []]],
        tags: [Tags::FLASHCARD],
        parameters: [
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
        IndexFlashcardCategoryRequest $request,
        GetMainCategory $get_main_category,
        GetUserCategories $get_user_categories,
    ): FlashcardCategoriesResource {
        return new FlashcardCategoriesResource([
            'main' => $get_main_category->handle(),
            'categories' => $get_user_categories->handle(
                new Owner(new OwnerId($request->getUserId()->getValue()), FlashcardOwnerType::USER),
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
        GetCategoryDetails $get_category_details,
    ): CategoryDetailsResource {
        $result = $generate_flashcards->handle($request->toCommand());

        return (new CategoryDetailsResource($get_category_details->get($result->getCategoryId(), $result->getGeneratedCount())))
            ->additional([
                'merged_to_existing_category' => $result->getMergedToExistingCategory(),
            ]);
    }

    #[OAT\Get(
        path: '/api/flashcards/categories/{category_id}',
        operationId: 'flashcards.categories.get',
        description: 'Get flashcard details for category',
        summary: 'Get flashcards and category details',
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
    public function get(
        Request $request,
        GetCategoryDetails $get_category_details,
    ): CategoryDetailsResource {
        return new CategoryDetailsResource($get_category_details->get(
            new CategoryId((int) $request->route('category_id')),
            null
        ));
    }
}
