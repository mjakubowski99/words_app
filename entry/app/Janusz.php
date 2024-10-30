<?php

namespace App;

class Janusz extends CustomModel
{
    public function retrieve()
    {
        return [
            ['id' => 1, 'password' => 'pass'],
            ['id' => 2, 'password' => 'pass'],
        ];
    }

    public function retrieveCount()
    {
        return 8;
    }
}
