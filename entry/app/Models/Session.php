<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static Builder|Session newModelQuery()
 * @method static Builder|Session newQuery()
 * @method static Builder|Session query()
 * @mixin \Eloquent
 */
class Session extends Model
{
    use HasFactory;
}
