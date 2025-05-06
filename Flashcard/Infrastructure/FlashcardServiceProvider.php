<?php

declare(strict_types=1);

namespace Flashcard\Infrastructure;

use Shared\Flashcard\IFlashcardFacade;
use Illuminate\Support\ServiceProvider;
use Shared\Flashcard\IFlashcardAdminFacade;
use Flashcard\Application\Facades\FlashcardFacade;
use Flashcard\Application\Services\IFlashcardSelector;
use Flashcard\Application\Facades\FlashcardAdminFacade;
use Flashcard\Application\Repository\ISessionRepository;
use Flashcard\Application\Services\IRepetitionAlgorithm;
use Flashcard\Application\Repository\IFlashcardRepository;
use Flashcard\Application\Repository\ISessionReadRepository;
use Flashcard\Application\Repository\IFlashcardDeckRepository;
use Flashcard\Application\Repository\IFlashcardPollRepository;
use Flashcard\Application\Repository\IFlashcardReadRepository;
use Flashcard\Application\Repository\ISmTwoFlashcardRepository;
use Flashcard\Application\Services\AiGenerators\GeminiGenerator;
use Flashcard\Application\Services\SmTwo\SmTwoFlashcardSelector;
use Flashcard\Application\Repository\IFlashcardDeckReadRepository;
use Flashcard\Application\Repository\IActiveSessionFlashcardsRepository;
use Flashcard\Application\Services\SmTwo\SmTwoRepetitionAlgorithm;
use Flashcard\Application\Repository\IFlashcardDuplicateRepository;
use Flashcard\Application\Services\AiGenerators\IFlashcardGenerator;
use Flashcard\Application\Repository\ISessionFlashcardReadRepository;
use Flashcard\Infrastructure\Repositories\Postgres\SessionRepository;
use Flashcard\Application\Repository\INextSessionFlashcardsRepository;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardRepository;
use Flashcard\Infrastructure\Repositories\Postgres\SessionReadRepository;
use Flashcard\Application\Repository\IRateableSessionFlashcardsRepository;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckRepository;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardPollRepository;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardReadRepository;
use Flashcard\Infrastructure\Repositories\Postgres\SmTwoFlashcardRepository;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDeckReadRepository;
use Flashcard\Infrastructure\Repositories\Postgres\ActiveSessionFlashcardsRepository;
use Flashcard\Infrastructure\Repositories\Postgres\FlashcardDuplicateRepository;
use Flashcard\Infrastructure\Repositories\Postgres\SessionFlashcardReadRepository;
use Flashcard\Infrastructure\Repositories\Postgres\NextSessionFlashcardsRepository;
use Flashcard\Infrastructure\Repositories\Postgres\RateableSessionFlashcardsRepository;

class FlashcardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IFlashcardFacade::class, FlashcardFacade::class);
        $this->app->bind(ISessionRepository::class, SessionRepository::class);
        $this->app->bind(ISessionReadRepository::class, SessionReadRepository::class);
        $this->app->bind(IRateableSessionFlashcardsRepository::class, RateableSessionFlashcardsRepository::class);
        $this->app->bind(INextSessionFlashcardsRepository::class, NextSessionFlashcardsRepository::class);
        $this->app->bind(ISessionFlashcardReadRepository::class, SessionFlashcardReadRepository::class);
        $this->app->bind(IFlashcardDeckRepository::class, FlashcardDeckRepository::class);
        $this->app->bind(IFlashcardDeckRepository::class, FlashcardDeckRepository::class);
        $this->app->bind(IRepetitionAlgorithm::class, SmTwoRepetitionAlgorithm::class);
        $this->app->bind(IFlashcardSelector::class, SmTwoFlashcardSelector::class);
        $this->app->bind(ISmTwoFlashcardRepository::class, SmTwoFlashcardRepository::class);
        $this->app->bind(IFlashcardRepository::class, FlashcardRepository::class);
        $this->app->bind(IFlashcardGenerator::class, GeminiGenerator::class);
        $this->app->bind(IFlashcardDeckReadRepository::class, FlashcardDeckReadRepository::class);
        $this->app->bind(IFlashcardReadRepository::class, FlashcardReadRepository::class);
        $this->app->bind(IFlashcardAdminFacade::class, FlashcardAdminFacade::class);
        $this->app->bind(IFlashcardDuplicateRepository::class, FlashcardDuplicateRepository::class);
        $this->app->bind(IFlashcardPollRepository::class, FlashcardPollRepository::class);
        $this->app->bind(IActiveSessionFlashcardsRepository::class, ActiveSessionFlashcardsRepository::class);
    }
}
