<?php

namespace Flashcard\Infrastructure\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;
use Shared\Exercise\IUnscrambleWordExercise;

/** @property IUnscrambleWordExercise $resource */
class UnscrambleWordExerciseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getAnswerEntryId(),
            'word' => $this->resource->getWord(),
            'context' => $this->resource->getContext(),
            'scrambled_word' => $this->resource->getScrambledWord(),
        ];
    }
}