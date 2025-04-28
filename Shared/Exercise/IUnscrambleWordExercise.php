<?php

namespace Shared\Exercise;

interface IUnscrambleWordExercise
{
    public function getAnswerEntryId(): int;
    public function getWord(): string;
    public function getContext(): string;
    public function getScrambledWord(): string;
}