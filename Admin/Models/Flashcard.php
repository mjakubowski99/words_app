<?php

declare(strict_types=1);

namespace Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Flashcard extends Model
{
    protected $fillable = [
        'front_word',
        'back_word',
        'front_context',
        'back_context',
    ];

    public static function booted(): void
    {
        static::addGlobalScope('admin', function (Builder $builder) {
            $builder->whereNull('user_id')
                ->whereNotNull('admin_id');
        });
    }

    public function update(array $attributes = [], array $options = [])
    {
        throw new \Exception('Operation not allowed directly');
    }

    public function delete(array $attributes = [], array $options = [])
    {
        throw new \Exception('Operation not allowed directly');
    }
}
