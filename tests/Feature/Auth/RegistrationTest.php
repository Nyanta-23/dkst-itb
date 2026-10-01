<?php

use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    Unit::create(['code' => 'EXT', 'name' => 'Mitra Eksternal', 'type' => 'external']);
    Role::create(['name' => 'eksternal']);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->first();
    $response->assertRedirect(route('dashboard'));
});

test('newly registered users are assigned the eksternal role and unit', function () {
    $externalUnit = Unit::create(['code' => 'EXT', 'name' => 'Mitra Eksternal', 'type' => 'external']);
    Role::create(['name' => 'eksternal']);

    $this->post(route('register.store'), [
        'name' => 'Mitra Baru',
        'email' => 'mitra-baru@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'mitra-baru@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->unit_id)->toBe($externalUnit->id);
    expect($user->hasRole('eksternal'))->toBeTrue();
});
