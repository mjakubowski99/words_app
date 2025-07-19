<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\StoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Story extends Model
{
    use HasFactory;
}
