<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure\Http\Controllers;

use Flashcard\Application\Query\GetMainCategory;
use Flashcard\Application\Query\GetUserCategories;
use Flashcard\Infrastructure\Http\Request\IndexFlashcardCategoryRequest;
use Flashcard\Infrastructure\Http\Resources\FlashcardCategoriesResource;

class FlashcardCategoryController
{
    public function index(
        IndexFlashcardCategoryRequest $request,
        GetMainCategory $get_main_category,
        GetUserCategories $get_user_categories,
    ): FlashcardCategoriesResource {
        return new FlashcardCategoriesResource([
            'main' => $get_main_category->handle(),
            'categories' => $get_user_categories->handle(
                $request->getUserId(),
                $request->getPage(),
                $request->getPerPage(),
            ),
            'page' => $request->input('page'),
            'per_page' => $request->input('per_page'),
        ]);
    }
}
