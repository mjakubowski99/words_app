<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure;

use Flashcard\Application\Repository\ISessionFlashcardReadRepository;
use Flashcard\Application\Repository\ISessionReadRepository;
use Flashcard\Infrastructure\Repositories\SessionFlashcardReadRepository;
use Flashcard\Infrastructure\Repositories\SessionReadRepository;
use Illuminate\Support\ServiceProvider;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Application\Repository\ISessionRepository;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Infrastructure\Repositories\SessionRepository;
use Flashcard\Infrastructure\Repositories\FlashcardRepository;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;
use Flashcard\Application\Services\AiGenerators\GeminiGenerator;
use Flashcard\Application\Services\SmTwo\SmTwoFlashcardSelector;
use Flashcard\Application\Repository\ISessionFlashcardRepository;
use Flashcard\Application\Repository\IFlashcardCategoryRepository;
use Flashcard\Application\Services\SmTwo\SmTwoRepetitionAlgorithm;
use Flashcard\Infrastructure\Repositories\SmTwoFlashcardRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;
use Flashcard\Infrastructure\Repositories\SessionFlashcardRepository;
use Flashcard\Application\Repository\INextSessionFlashcardsRepository;
use Flashcard\Infrastructure\Repositories\FlashcardCategoryRepository;
use Flashcard\Application\Repository\IRateableSessionFlashcardsRepository;
use Flashcard\Infrastructure\Repositories\NextSessionFlashcardsRepository;
use Flashcard\Infrastructure\Repositories\RateableSessionFlashcardsRepository;

class FlashcardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ISessionRepository::class, SessionRepository::class);
        $this->app->bind(ISessionReadRepository::class, SessionReadRepository::class);
        $this->app->bind(IRateableSessionFlashcardsRepository::class, RateableSessionFlashcardsRepository::class);
        $this->app->bind(INextSessionFlashcardsRepository::class, NextSessionFlashcardsRepository::class);
        $this->app->bind(ISessionFlashcardRepository::class, SessionFlashcardRepository::class);
        $this->app->bind(ISessionFlashcardReadRepository::class, SessionFlashcardReadRepository::class);
        $this->app->bind(IFlashcardCategoryRepository::class, FlashcardCategoryRepository::class);
        $this->app->bind(IFlashcardCategoryRepository::class, FlashcardCategoryRepository::class);
        $this->app->bind(IRepetitionAlgorithm::class, SmTwoRepetitionAlgorithm::class);
        $this->app->bind(IFlashcardSelector::class, SmTwoFlashcardSelector::class);
        $this->app->bind(ISmTwoFlashcardRepository::class, SmTwoFlashcardRepository::class);
        $this->app->bind(IFlashcardRepository::class, FlashcardRepository::class);
        $this->app->bind(IFlashcardGenerator::class, GeminiGenerator::class);
    }
}
