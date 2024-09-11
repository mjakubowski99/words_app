<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers;

use Flashcard\Domain\Models\Owner;
use Flashcard\Domain\Models\OwnerId;
use OpenApi\Attributes as OAT;
use App\Http\OpenApi\Tags;
use Flashcard\Application\Command\GenerateFlashcards;
use Flashcard\Application\Command\GenerateFlashcardsHandler;
use Flashcard\Application\Query\GetCategoryDetails;
use Flashcard\Application\Query\GetMainCategory;
use Flashcard\Application\Query\GetUserCategories;
use Flashcard\Infrastructure\Http\Request\GenerateFlashcardsRequest;
use Flashcard\Infrastructure\Http\Request\IndexFlashcardCategoryRequest;
use Flashcard\Infrastructure\Http\Resources\CategoryDetailsResource;
use Flashcard\Infrastructure\Http\Resources\FlashcardCategoriesResource;
use Shared\Enum\FlashcardOwnerType;

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

    public function generateFlashcards(
        GenerateFlashcardsRequest $request,
        GenerateFlashcardsHandler $generate_flashcards,
        GetCategoryDetails $get_category_details,
    ): CategoryDetailsResource {
        $command = new GenerateFlashcards(
            new Owner(new OwnerId($request->current()->getId()->getValue()), FlashcardOwnerType::USER),
            $request->getCategoryName()
        );

        $category_id = $generate_flashcards->handle($command);

        return new CategoryDetailsResource($get_category_details->get($category_id));
    }
}
