<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property        int                                                 $id
 * @property        null|Carbon                                         $created_at
 * @property        null|Carbon                                         $updated_at
 * @method   static \Database\Factories\StoryFactory                    factory($count = null, $state = [])
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Story newModelQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Story newQuery()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Story query()
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Story whereCreatedAt($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Story whereId($value)
 * @method   static \Illuminate\Database\Eloquent\Builder<static>|Story whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Story extends Model
{
    use HasFactory;
}
