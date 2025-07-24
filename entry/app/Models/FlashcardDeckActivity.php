<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                                                                 $id
 * @property        int                                                                 $flashcard_deck_id
 * @property        string                                                              $user_id
 * @property        null|string                                                         $last_viewed_at
 * @method   static \Database\Factories\FlashcardDeckActivityFactory                    factory($count = null, $state = [])
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeckActivity newModelQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeckActivity newQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeckActivity query()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeckActivity whereFlashcardDeckId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeckActivity whereId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeckActivity whereLastViewedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|FlashcardDeckActivity whereUserId($value)
 * @mixin \Eloquent
 */
class FlashcardDeckActivity extends Model
{
    use HasFactory;

    public $timestamps = false;
}
