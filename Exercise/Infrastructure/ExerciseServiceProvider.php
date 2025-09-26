<?php

declare(strict_types=1);

namespace Exercise\Infrastructure;

use Exercise\Application\Facades\ExerciseReadFacade;
use Exercise\Application\Facades\FlashcardExerciseFacade;
use Exercise\Application\Repositories\Exercise\IUnscrambleWordExerciseRepository;
use Exercise\Application\Repositories\Exercise\IWordMatchExerciseRepository;
use Exercise\Application\Repositories\ExerciseRead\IUnscrambleWordExerciseReadRepository;
use Exercise\Application\Repositories\ExerciseRead\IWordMatchExerciseReadRepository;
use Exercise\Application\Repositories\IExerciseStatsRepository;
use Exercise\Infrastructure\Repositories\ExerciseStatsRepository;
use Exercise\Infrastructure\Repositories\UnscrambleWordExerciseReadRepository;
use Exercise\Infrastructure\Repositories\UnscrambleWordExerciseRepository;
use Exercise\Infrastructure\Repositories\WordMatchExerciseReadRepository;
use Exercise\Infrastructure\Repositories\WordMatchExerciseRepository;
use Illuminate\Support\ServiceProvider;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Shared\Exercise\IFlashcardExerciseFacade;

class ExerciseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IFlashcardExerciseFacade::class, FlashcardExerciseFacade::class);
        $this->app->bind(IUnscrambleWordExerciseRepository::class, UnscrambleWordExerciseRepository::class);
        $this->app->bind(IUnscrambleWordExerciseReadRepository::class, UnscrambleWordExerciseReadRepository::class);
        $this->app->bind(IExerciseReadFacade::class, ExerciseReadFacade::class);
        $this->app->bind(IWordMatchExerciseRepository::class, WordMatchExerciseRepository::class);
        $this->app->bind(IWordMatchExerciseReadRepository::class, WordMatchExerciseReadRepository::class);
        $this->app->bind(IExerciseStatsRepository::class, ExerciseStatsRepository::class);
    }
}
