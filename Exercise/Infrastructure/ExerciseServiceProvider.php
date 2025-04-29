<?php

namespace Exercise\Infrastructure;

use Exercise\Application\Facades\ExerciseFacade;
use Exercise\Application\Repositories\IUnscrambleWordExerciseRepository;
use Exercise\Infrastructure\Repositories\UnscrambleWordExerciseRepository;
use Illuminate\Support\ServiceProvider;
use Shared\Exercise\IExerciseFacade;

class ExerciseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IExerciseFacade::class, ExerciseFacade::class);
        $this->app->bind(IUnscrambleWordExerciseRepository::class, UnscrambleWordExerciseRepository::class);
    }
}