<?php

declare(strict_types=1);

namespace Tests;

use Mockery\MockInterface;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return MockInterface|T
     */
    public function mockery(string $class, array $allows): mixed
    {
        $mock = \Mockery::mock($class);
        $mock->allows($allows);

        return $mock;
    }
}
