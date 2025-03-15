<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlashcardPollItem extends Model
{
    use HasFactory;

    protected $table = 'flashcard_poll_items';

    protected $guarded = [];

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class, 'flashcard_id');
    }
}
