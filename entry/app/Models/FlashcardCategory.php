<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Flashcard\Domain\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Flashcard\Domain\ValueObjects\CategoryId;
use Database\Factories\FlashcardCategoryFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                       $id
 * @property        string                    $tag
 * @property        string                    $name
 * @property        string                    $user_id
 * @property        null|Carbon               $created_at
 * @property        null|Carbon               $updated_at
 * @method   static FlashcardCategoryFactory  factory($count = null, $state = [])
 * @method   static Builder|FlashcardCategory newModelQuery()
 * @method   static Builder|FlashcardCategory newQuery()
 * @method   static Builder|FlashcardCategory query()
 * @method   static Builder|FlashcardCategory whereCreatedAt($value)
 * @method   static Builder|FlashcardCategory whereId($value)
 * @method   static Builder|FlashcardCategory whereName($value)
 * @method   static Builder|FlashcardCategory whereTag($value)
 * @method   static Builder|FlashcardCategory whereUpdatedAt($value)
 * @method   static Builder|FlashcardCategory whereUserId($value)
 * @mixin \Eloquent
 */
class FlashcardCategory extends Model
{
    use HasFactory;

    protected $table = 'flashcard_categories';

    public function getId(): CategoryId
    {
        return new CategoryId($this->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toDomainModel(): Category
    {
        return (new Category(
            $this->user->toOwner(),
            $this->tag,
            $this->name,
        ))->init(new CategoryId($this->id));
    }
}
