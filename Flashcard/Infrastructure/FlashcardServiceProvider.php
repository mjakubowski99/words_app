<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure;

use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Infrastructure\DatabaseRepositories\FlashcardCategoryRepository;
use Flashcard\Infrastructure\DatabaseRepositories\SessionRepository;
use Illuminate\Support\ServiceProvider;

class FlashcardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ISessionRepository::class, SessionRepository::class);
        $this->app->bind(IFlashcardCategoryRepository::class, FlashcardCategoryRepository::class);
    }
}