<?php

declare(strict_types=1);

namespace Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashcardDeck extends Model
{
    protected $fillable = [
        'name',
    ];

    public static function booted(): void
    {
        static::addGlobalScope('admin', function (Builder $builder) {
            $builder->whereNull('user_id')
                ->whereNotNull('admin_id');
        });
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }
}
