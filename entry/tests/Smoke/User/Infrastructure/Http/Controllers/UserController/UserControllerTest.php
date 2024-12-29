<?php

declare(strict_types=1);

namespace Tests\Smoke\User\Infrastructure\Http\Controllers\UserController;

use App\Models\Flashcard;
use Shared\Enum\ReportableType;
use Shared\Enum\TicketType;
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
    public function loginWithProvider_WhenPlatformIsAndroid_success(): void
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
    public function loginWithProvider_WhenPlatformIsIos_success(): void
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

    /**
     * @test
     */
    public function delete_WhenUserAuthenticatedAndValidEmail_success(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->deleteJson(route('user.me.delete'), [
                'email' => $email,
            ]);

        // THEN
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function delete_WhenUserAuthenticatedAndInvalidEmail_fail(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email
        ]);

        // WHEN
        $response = $this->actingAs($user)
            ->deleteJson(route('user.me.delete'), [
                'email' => 'other@email.com',
            ]);

        // THEN
        $response->assertStatus(400);
    }


    /**
     * @test
     */
    public function delete_WhenUserNotAuthenticated_unauthenticated()
    {
        // GIVEN

        // WHEN
        $response = $this->deleteJson(route('user.me.delete'));

        // THEN
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function storeTicket_WhenUserNotAuthenticated_success(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email
        ]);

        // WHEN
        $response = $this
            ->postJson(route('tickets.store'), [
                'email' => $email,
                'type' => TicketType::DELETE_ACCOUNT,
                'description' => 'Desc 5',
            ]);

        // THEN
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function storeTicket_WhenUserAuthenticated_success(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email
        ]);
        $flashcard = Flashcard::factory()->create();

        // WHEN
        $response = $this->actingAs($user)
            ->postJson(route('tickets.store'), [
                'type' => TicketType::INAPPROPRIATE_CONTENT,
                'description' => 'Inappropriate content',
                'reportable_id' => $flashcard->id,
                'reportable_type' => ReportableType::FLASHCARD,
            ]);

        // THEN
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function storeTicket_WhenDescriptionTooShort_validationError(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email
        ]);

        // WHEN
        $response = $this
            ->postJson(route('tickets.store'), [
                'email' => $email,
                'type' => TicketType::DELETE_ACCOUNT,
                'description' => 'd',
            ]);

        // THEN
        $response->assertStatus(422);
    }
}
