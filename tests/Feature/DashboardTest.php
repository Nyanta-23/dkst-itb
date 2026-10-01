<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users with dashboard.view can visit the dashboard', function () {
    Permission::create(['name' => 'dashboard.view']);
    $user = User::factory()->create();
    $user->givePermissionTo('dashboard.view');

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('authenticated users without dashboard.view are forbidden', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertForbidden();
});
