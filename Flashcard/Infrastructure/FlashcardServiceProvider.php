<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure;

use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Domain\Services\IRepetitionAlgorithm;
use Flashcard\Domain\Services\SmTwo\SmTwoRepetitionAlgorithm;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardCategoryRepository;
use Flashcard\Infrastructure\Repositories\Postgres\SessionFlashcardRepository;
use Flashcard\Infrastructure\Repositories\Postgres\SessionRepository;
use Flashcard\Infrastructure\Repositories\Postgres\SmTwoFlashcardRepository;
use Illuminate\Support\ServiceProvider;

class FlashcardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ISessionRepository::class, SessionRepository::class);
        $this->app->bind(ISessionFlashcardRepository::class, SessionFlashcardRepository::class);
        $this->app->bind(IFlashcardCategoryRepository::class, FlashcardCategoryRepository::class);
        $this->app->bind(IFlashcardCategoryRepository::class, FlashcardCategoryRepository::class);
        $this->app->bind(IRepetitionAlgorithm::class, SmTwoRepetitionAlgorithm::class);
        $this->app->bind(ISmTwoFlashcardRepository::class, SmTwoFlashcardRepository::class);
    }
}