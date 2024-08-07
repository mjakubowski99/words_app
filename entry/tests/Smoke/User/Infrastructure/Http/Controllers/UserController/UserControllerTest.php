<?php

declare(strict_types=1);

namespace Tests\Smoke\User\Infrastructure\Http\Controllers\UserController;

use Tests\TestCase;
use App\Models\User;
use Shared\Enum\UserProvider;
use Shared\Utils\ValueObjects\Uuid;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    use UserControllerTrait;

    /**
     * @test
     */
    public function initFirebaseUser_WhenFirebaseUserAndUserNotExists_createUser(): void
    {
        // GIVEN
        $user = $this->createFirebaseUser(Uuid::make()->getValue(), UserProvider::GOOGLE);

        // WHEN
        $response = $this->actingAs($user, 'firebase')
            ->postJson(route('user.firebase-init'));

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
    public function initFirebaseUser_WhenFirebaseUserAndUserExists_success(): void
    {
        // GIVEN
        $user = $this->createFirebaseUser(Uuid::make()->getValue(), UserProvider::GOOGLE);
        User::factory()->create([
            'provider_id' => $user->getProviderId(),
            'provider_type' => UserProvider::GOOGLE,
        ]);

        // WHEN
        $response = $this->actingAs($user, 'firebase')
            ->postJson(route('user.firebase-init'));

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
    public function me_WhenFirebaseUserAndUserNotExists_notFoundException(): void
    {
        // GIVEN
        $user = $this->createFirebaseUser(Uuid::make()->getValue(), UserProvider::GOOGLE);

        // WHEN
        $response = $this->actingAs($user, 'firebase')
            ->getJson(route('user.me'));

        // THEN
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
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
    public function me_WhenFirebaseUserAndUserExists_success(): void
    {
        // GIVEN
        $user = $this->createFirebaseUser(Uuid::make()->getValue(), UserProvider::GOOGLE);
        User::factory()->create([
            'provider_id' => $user->getProviderId(),
            'provider_type' => UserProvider::GOOGLE,
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
        $response = $this->getJson(route('user.me'));

        // THEN
        $response->assertStatus(401);
    }
}
