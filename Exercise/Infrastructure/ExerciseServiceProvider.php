<?php

declare(strict_types=1);

namespace Exercise\Infrastructure;

use Illuminate\Support\ServiceProvider;
use Shared\Exercise\IFlashcardExerciseFacade;
use Shared\Exercise\Exercises\IExerciseReadFacade;
use Exercise\Application\Facades\ExerciseReadFacade;
use Exercise\Application\Facades\FlashcardExerciseFacade;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Infrastructure\Repositories\UnscrambleWordExerciseRepository;
use Exercise\Application\Repositories\IUnscrambleWordExerciseReadRepository;
use Exercise\Infrastructure\Repositories\UnscrambleWordExerciseReadRepository;

class ExerciseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IFlashcardExerciseFacade::class, FlashcardExerciseFacade::class);
        $this->app->bind(IUnscrambleWordExerciseRepository::class, UnscrambleWordExerciseRepository::class);
        $this->app->bind(IUnscrambleWordExerciseReadRepository::class, UnscrambleWordExerciseReadRepository::class);
        $this->app->bind(IExerciseReadFacade::class, ExerciseReadFacade::class);
    }
}
