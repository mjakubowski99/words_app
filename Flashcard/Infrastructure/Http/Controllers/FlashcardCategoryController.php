<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers;

use App\Http\OpenApi\Tags;
use OpenApi\Attributes as OAT;
use Flashcard\Domain\Models\Owner;
use Shared\Enum\FlashcardOwnerType;
use Flashcard\Domain\ValueObjects\OwnerId;
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
    #[OAT\Post(
        path: '/api/flashcards/categories/by-user',
        operationId: 'flashcards.categories.index',
        description: 'Get user flashcard categories',
        summary: 'Get user flashcard categories',
        security: [['firebase' => []]],
        requestBody: new OAT\RequestBody(
            content: new OAT\JsonContent(ref: '#/components/schemas/Requests\Flashcard\IndexFlashcardCategoryRequest')
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
                        items: new OAT\Items(ref: '#/components/schemas/Resources\Flashcard\FlashcardCategoriesResource'),
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
            'page' => $request->input('page'),
            'per_page' => $request->input('per_page'),
        ]);
    }

    #[OAT\Post(
        path: 'api/flashcards/generate-by-category',
        operationId: 'flashcards.generate-by-category',
        description: 'Generate flashcards by provided category',
        summary: 'Generate flashcards by provided category',
        security: [['firebase' => []]],
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
    public function generateFlashcards(
        GenerateFlashcardsRequest $request,
        GenerateFlashcardsHandler $generate_flashcards,
        GetCategoryDetails $get_category_details,
    ): CategoryDetailsResource {
        $category_id = $generate_flashcards->handle($request->toCommand());

        return new CategoryDetailsResource($get_category_details->get($category_id));
    }
}
