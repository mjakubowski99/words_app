<?php

declare(strict_types=1);
use App\Models\Flashcard;
use Shared\Enum\ReportType;
use Shared\Enum\ReportableType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Shared\Utils\ValueObjects\Language;

uses(DatabaseTransactions::class);

test('delete when user authenticated and valid email success', function () {
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
});
test('delete when user authenticated and invalid email fail', function () {
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
});
test('delete when user not authenticated unauthenticated', function () {
    // GIVEN
    // WHEN
    $response = $this->deleteJson(route('user.me.delete'));

    // THEN
    $response->assertStatus(401);
});
test('store report when user not authenticated success', function () {
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
});
test('store report when user authenticated success', function () {
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
});
test('store report when description too short validation error', function () {
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
});

test('update language updates user language', function () {
    // GIVEN
    $user = $this->createUser();

    // WHEN
    $response = $this
        ->actingAs($user)
        ->putJson(route('user.me.language.update'), [
            'user_language' => Language::es()->getValue(),
            'learning_language' => Language::it()->getValue(),
        ]);

    // THEN
    $response->assertStatus(204);
});
