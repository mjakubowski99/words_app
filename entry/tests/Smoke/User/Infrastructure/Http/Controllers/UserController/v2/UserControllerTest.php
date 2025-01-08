<?php

declare(strict_types=1);

namespace Smoke\User\Infrastructure\Http\Controllers\UserController\v2;

use Tests\TestCase;
use App\Models\Flashcard;
use Shared\Enum\ReportType;
use Shared\Enum\ReportableType;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function delete_WhenUserAuthenticatedAndValidEmail_success(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email,
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
            'email' => $email,
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
    public function storeReport_WhenUserNotAuthenticated_success(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email,
        ]);

        // WHEN
        $response = $this
            ->postJson(route('reports.store'), [
                'email' => $email,
                'type' => ReportType::DELETE_ACCOUNT,
                'description' => 'Desc 5',
            ]);

        // THEN
        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function storeReport_WhenUserAuthenticated_success(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email,
        ]);
        $flashcard = Flashcard::factory()->create();

        // WHEN
        $response = $this->actingAs($user)
            ->postJson(route('reports.store'), [
                'type' => ReportType::INAPPROPRIATE_CONTENT,
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
    public function storeReport_WhenDescriptionTooShort_validationError(): void
    {
        // GIVEN
        $email = 'email@email.com';
        $user = $this->createUser([
            'email' => $email,
        ]);

        // WHEN
        $response = $this
            ->postJson(route('reports.store'), [
                'email' => $email,
                'type' => ReportType::DELETE_ACCOUNT,
                'description' => 'd',
            ]);

        // THEN
        $response->assertStatus(422);
    }
}
