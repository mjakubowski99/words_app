<?php

namespace App\Models;

use Flashcard\Domain\Models\FlashcardId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shared\Utils\ValueObjects\UserId;

class SmTwoFlashcard extends Model
{
    use HasFactory;

    public $incrementing = false;

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function getFlashcardId(): FlashcardId
    {
        return new FlashcardId($this->flashcard_id);
    }

    public function toDomainModel(): \Flashcard\Domain\Models\SmTwoFlashcard
    {
        return new \Flashcard\Domain\Models\SmTwoFlashcard(
            new UserId($this->user_id),
            $this->flashcard->toDomainModel(),
            $this->repetition_ratio,
            $this->repetition_interval,
            $this->repetition_count,
        );
    }
}
