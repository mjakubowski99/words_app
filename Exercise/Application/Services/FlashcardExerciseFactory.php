<?php

declare(strict_types=1);

namespace Exercise\Application\Services;

use Exercise\Application\Services\ExerciseFactory\UnscrambleWordExerciseFactory;
use Exercise\Application\Services\ExerciseFactory\WordMatchExerciseFactory;
use Illuminate\Container\Container;
use Shared\Enum\ExerciseType;
use Shared\Flashcard\ISessionFlashcardSummaries;
use Shared\Utils\ValueObjects\UserId;

class FlashcardExerciseFactory
{
    public function __construct(
        private Container $app,
    ) {}

    public function makeExercise(ISessionFlashcardSummaries $summaries, UserId $user_id, ExerciseType $type): array
    {
        $factory = match ($type) {
            /* @phpstan-ignore-next-line */
            ExerciseType::UNSCRAMBLE_WORDS => $this->app->make(UnscrambleWordExerciseFactory::class),
            ExerciseType::WORD_MATCH => $this->app->make(WordMatchExerciseFactory::class),
            default => throw new \InvalidArgumentException('Invalid exercise type'),
        };

        return $factory->make($summaries, $user_id);
    }
}
