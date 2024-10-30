<?php

declare(strict_types=1);

namespace Tests\Smoke\User\Infrastructure\Http\Controllers\UserController;

use Tests\TestCase;
use App\Models\User;
use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Middleware\Authenticate;
use User\Infrastructure\OAuth\Google\IosGoogleClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use User\Infrastructure\OAuth\Google\AndroidGoogleClient;

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
                'platform' => Platform::WEB,
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
        $this->assertArrayHasKey('client_id', Config::get('services.google'));
        $this->assertArrayHasKey('client_secret', Config::get('services.google'));
    }

    /**
     * @test
     */
    public function loginWithProvider_WhenPlatformIsAndroid_updateConfigs(): void
    {
        // GIVEN
        $client = \Mockery::mock(AndroidGoogleClient::class);
        $client->shouldReceive('verifyIdToken')->andReturn([
            'sub' => '123',
            'name' => 'Pawel Kowal',
            'email' => 'email@email.com',
            'picture' => 'avatar.jpg',
        ]);
        $this->app->instance(AndroidGoogleClient::class, $client);

        // WHEN
        $response = $this
            ->postJson(route('user.oauth.login'), [
                'access_token' => 'adsadsdsa',
                'user_provider' => UserProvider::GOOGLE->value,
                'platform' => Platform::ANDROID,
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
    public function loginWithProvider_WhenPlatformIsIos_updateConfigs(): void
    {
        // GIVEN
        $client = \Mockery::mock(IosGoogleClient::class);
        $client->shouldReceive('verifyIdToken')->andReturn([
            'sub' => '123',
            'name' => 'Pawel Kowal',
            'email' => 'email@email.com',
            'picture' => 'avatar.jpg',
        ]);
        $this->app->instance(IosGoogleClient::class, $client);

        // WHEN
        $response = $this
            ->postJson(route('user.oauth.login'), [
                'access_token' => 'adsadsdsa',
                'user_provider' => UserProvider::GOOGLE->value,
                'platform' => Platform::IOS,
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
                'platform' => Platform::WEB->value,
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
