<?php

declare(strict_types=1);

namespace Tests\Smoke\User\Infrastructure\Http\Controllers\UserController;

use Tests\TestCase;
use App\Models\User;
use Shared\Enum\UserProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    use UserControllerTrait;

    /**
     * @test
     */
    public function loginWithProvider_WhenValidOAuthUserAndUserNotExists_createUser(): void
    {
        // GIVEN
        $user = $this->fakeSocialiteUser();
        $this->fakeOAuthLogin($user, UserProvider::GOOGLE);

        // WHEN
        $response = $this
            ->postJson(route('user.oauth.login'), [
                'access_token' => 'adsadsdsa',
                'user_provider' => UserProvider::GOOGLE->value,
            ]);

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function loginWithProvider_WhenValidUserAndUserExists_success(): void
    {
        // GIVEN
        $user = $this->fakeSocialiteUser();
        $this->fakeOAuthLogin($user, UserProvider::GOOGLE);
        User::factory()->create([
            'provider_id' => $user->getId(),
            'provider_type' => UserProvider::GOOGLE,
        ]);

        // WHEN
        $response = $this
            ->postJson(route('user.oauth.login'), [
                'access_token' => '1233212',
                'user_provider' => UserProvider::GOOGLE->value,
            ]);

        // THEN
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function me_WhenUserFromOtherDriver_success(): void
    {
        // GIVEN
        $user = $this->createUser();

        // WHEN
        $response = $this->actingAs($user, 'web')
            ->withoutMiddleware(Authenticate::class)
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
    public function me_WhenAuthorized_success(): void
    {
        // GIVEN
        $user = User::factory()->create();

        // WHEN
        $response = $this->actingAs($user, 'sanctum')
            ->getJson(route('user.me'));

        // THEN
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function me_WhenUserNotAuthenticated_unauthenticated()
    {
        // GIVEN

        // WHEN
        $response = $this->getJson(route('user.me'));

        // THEN
        $response->assertStatus(401);
    }
}
