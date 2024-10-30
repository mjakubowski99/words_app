<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class CustomModel extends Model
{
    public ?array $filters;
    public ?array $orders;
    public ?int $offset;
    public ?int $limit;

    public abstract function retrieve();

    public abstract function retrieveCount();

    public function newEloquentBuilder($query)
    {
        return new CustomEloquentBuilder($query);
    }
}
