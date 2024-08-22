<?php

namespace Flashcard\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionFlashcardsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [];
    }
}