<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Database\Factories\SmTwoFlashcardFactory;
use Flashcard\Domain\ValueObjects\FlashcardId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmTwoFlashcard extends Model
{
    use HasFactory;

    public $incrementing = false;

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFlashcardId(): FlashcardId
    {
        return new FlashcardId($this->flashcard_id);
    }

    public function toDomainModel(): \Flashcard\Domain\Models\SmTwoFlashcard
    {
        return new \Flashcard\Domain\Models\SmTwoFlashcard(
            $this->user->getId(),
            $this->getFlashcardId(),
            (float) $this->repetition_ratio,
            (float) $this->repetition_interval,
            $this->repetition_count,
            $this->min_rating,
            $this->repetitions_in_session,
        );
    }
}
