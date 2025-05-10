<?php

declare(strict_types=1);

namespace Shared\Exercise\Exercises;

use Shared\Models\Emoji;
use Shared\Utils\ValueObjects\ExerciseId;

interface IUnscrambleWordExerciseRead
{
    public function getId(): ExerciseId;

    public function getScrambledWord(): string;

    public function getFrontWord(): string;

    public function getContextSentence(): string;

    public function getEmoji(): ?Emoji;

    /** @return string[] */
    public function getKeyboard(): array;

    public function getExerciseEntryId(): int;
}
