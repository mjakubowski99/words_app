<?php

declare(strict_types=1);

namespace Shared\Exercise\Exercises;

use Shared\Utils\ValueObjects\ExerciseEntryId;

interface IWordMatchExerciseReadEntry
{
    public function getExerciseEntryId(): ExerciseEntryId;

    public function getWord(): string;

    public function getWordTranslation(): string;

    public function getSentence(): string;

    public function getSentencePartBeforeWord(): string;

    public function getSentencePartAfterWord(): string;
}
