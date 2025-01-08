<?php

declare(strict_types=1);

namespace User\Infrastructure\Entities;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    protected $guarded = [];
}
