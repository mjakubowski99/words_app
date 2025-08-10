<?php

declare(strict_types=1);

namespace Exercise\Application\Services;

use Shared\Enum\ExerciseType;
use Illuminate\Container\Container;
use Shared\Utils\ValueObjects\UserId;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Exercise\Application\Services\ExerciseFactory\WordMatchExerciseFactory;
use Exercise\Application\Services\ExerciseFactory\UnscrambleWordExerciseFactory;

class FlashcardExerciseFactory
{
    public function __construct(
        private Container $app,
    ) {}

    public function makeExercise(ISessionFlashcardSummaries $summaries, UserId $user_id, ExerciseType $type): array
    {
        $factory = match ($type) {
            ExerciseType::UNSCRAMBLE_WORDS => $this->app->make(UnscrambleWordExerciseFactory::class),
            /* @phpstan-ignore-next-line */
            ExerciseType::WORD_MATCH => $this->app->make(WordMatchExerciseFactory::class),
            default => throw new \InvalidArgumentException('Invalid exercise type'),
        };

        return $factory->make($summaries, $user_id);
    }
}
