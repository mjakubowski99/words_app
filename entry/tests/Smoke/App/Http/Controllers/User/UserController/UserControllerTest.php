<?php

declare(strict_types=1);

namespace Tests\Smoke\App\Http\Controllers\User\UserController;

use Tests\TestCase;
use Auth\Infrastructure\Entities\FirebaseAuthenticable;

class UserControllerTest extends TestCase
{
    /**
     * @test
     */
    public function me_WhenFirebaseUserAndUserNotExists_createUserAndAuthenticate()
    {
        // GIVEN
        $user = new FirebaseAuthenticable();
        $user->updateOrCreateUser('123', [
            'email' => 'email@email.com',
            'name' => 'Przemek',
            'picture' => 'image.jpg',
            'sign_in_provider' => 'google.com',
        ]);

        // WHEN
        $response = $this->actingAs($user, 'firebase')
            ->getJson(route('user.me'));

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
        ]);
    }

    /**
     * @test
     */
    public function me_WhenUserNotAuthenticated_unauthenticated()
    {
        // GIVEN

        // WHEN
        $response = $this
            ->getJson(route('user.me'));

        // THEN
        $response->assertStatus(401);
    }
}
