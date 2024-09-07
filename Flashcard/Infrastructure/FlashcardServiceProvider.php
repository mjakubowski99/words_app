<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Flashcard\Domain\Services\IFlashcardSelector;
use Flashcard\Domain\Services\IRepetitionAlgorithm;
use Flashcard\Domain\Repositories\ISessionRepository;
use Flashcard\Domain\Repositories\IFlashcardRepository;
use Flashcard\Domain\Services\SmTwo\SmTwoFlashcardSelector;
use Flashcard\Domain\Repositories\ISmTwoFlashcardRepository;
use Flashcard\Infrastructure\Repositories\SessionRepository;
use Flashcard\Domain\Services\SmTwo\SmTwoRepetitionAlgorithm;
use Flashcard\Domain\Repositories\ISessionFlashcardRepository;
use Flashcard\Infrastructure\Repositories\FlashcardRepository;
use Flashcard\Domain\Repositories\IFlashcardCategoryRepository;
use Flashcard\Infrastructure\Repositories\SmTwoFlashcardRepository;
use Flashcard\Infrastructure\Repositories\SessionFlashcardRepository;
use Flashcard\Infrastructure\Repositories\FlashcardCategoryRepository;

class FlashcardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ISessionRepository::class, SessionRepository::class);
        $this->app->bind(ISessionFlashcardRepository::class, SessionFlashcardRepository::class);
        $this->app->bind(IFlashcardCategoryRepository::class, FlashcardCategoryRepository::class);
        $this->app->bind(IFlashcardCategoryRepository::class, FlashcardCategoryRepository::class);
        $this->app->bind(IRepetitionAlgorithm::class, SmTwoRepetitionAlgorithm::class);
        $this->app->bind(IFlashcardSelector::class, SmTwoFlashcardSelector::class);
        $this->app->bind(ISmTwoFlashcardRepository::class, SmTwoFlashcardRepository::class);
        $this->app->bind(IFlashcardRepository::class, FlashcardRepository::class);
    }
}
