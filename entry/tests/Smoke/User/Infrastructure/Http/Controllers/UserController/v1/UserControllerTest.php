<?php

declare(strict_types=1);
use App\Models\User;
use Firebase\JWT\JWT;
use Shared\Enum\Platform;
use Shared\Enum\UserProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Middleware\Authenticate;
use User\Infrastructure\OAuth\Google\IosGoogleClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use User\Infrastructure\OAuth\Google\AndroidGoogleClient;
use Tests\Smoke\User\Infrastructure\Http\Controllers\UserController\v1\UserControllerTrait;

uses(DatabaseTransactions::class);

uses(UserControllerTrait::class);

test('login with provider when valid oauth user and user not exists create user', function () {
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
                'has_any_session',
            ],
        ],
    ]);
    expect(Config::get('services.google'))->toHaveKey('client_id');
    expect(Config::get('services.google'))->toHaveKey('client_secret');
});
test('login with provider when valid oauth user apple and user not exists create user', function () {
    // GIVEN
    $user = $this->fakeSocialiteUser();
    $this->fakeOAuthLogin($user, UserProvider::APPLE);
    Mockery::mock('alias:' . JWT::class)
        ->shouldReceive('encode')
        ->andReturn('DSADSAADSASD');

    // WHEN
    $response = $this
        ->postJson(route('user.oauth.login'), [
            'access_token' => 'adsadsdsa',
            'user_provider' => UserProvider::APPLE->value,
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
                'has_any_session',
            ],
        ],
    ]);
});
test('login with provider when platform is android success', function () {
    // GIVEN
    $client = Mockery::mock(AndroidGoogleClient::class);
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
                'has_any_session',
            ],
        ],
    ]);
});
test('login with provider when platform is ios success', function () {
    // GIVEN
    $client = Mockery::mock(IosGoogleClient::class);
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
                'has_any_session',
            ],
        ],
    ]);
});
test('login with provider when valid user and user exists success', function () {
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
                'has_any_session',
            ],
        ],
    ]);
});
test('me when user from other driver success', function () {
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
            'has_any_session',
        ],
    ]);
});
test('me when authorized success', function () {
    // GIVEN
    $user = User::factory()->create();

    // WHEN
    $response = $this->actingAs($user, 'sanctum')
        ->getJson(route('user.me'));

    // THEN
    $response->assertStatus(200);
});
test('me when user not authenticated unauthenticated', function () {
    // GIVEN
    // WHEN
    $response = $this->getJson(route('user.me'));

    // THEN
    $response->assertStatus(401);
});
