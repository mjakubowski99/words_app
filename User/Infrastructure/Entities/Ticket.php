<?php

namespace User\Infrastructure\Entities;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $guarded = [];

    protected $casts = [
        'context' => ['array']
    ];
}