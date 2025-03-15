<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Flashcard\Domain\ValueObjects\SessionFlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LearningSessionFlashcard extends Model
{
    use HasFactory;

    public function getId(): SessionFlashcardId
    {
        return new SessionFlashcardId($this->id);
    }

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }
}
