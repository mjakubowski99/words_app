<?php

declare(strict_types=1);

namespace Tests\Smoke\App\Http\Controllers\Auth\AuthController;

use Illuminate\Testing\TestResponse;

trait AuthControllerTrait
{
    public function assertAuthResponseSuccessful(TestResponse $response)
    {
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'email',
                ],
                'token',
            ],
        ]);
    }
}
