<?php

declare(strict_types=1);

namespace Shared\Utils\Str;

use Illuminate\Support\Str as IlluminateStr;

class Str implements IStr
{
    public function __construct(
        private readonly IlluminateStr $str
    ) {}

    public function random(int $length): string
    {
        return $this->str::random($length);
    }
}
