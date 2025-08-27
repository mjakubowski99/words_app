<?php

declare(strict_types=1);
use App\Models\User;
use Livewire\Livewire;
use Admin\Resources\UserResource\Pages\ListUsers;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('success', function () {
    $users = [User::factory()->create(), User::factory()->create()];

    Livewire::test(ListUsers::class)->assertSuccessful();
});
