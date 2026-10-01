<?php

use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::create(['name' => 'ruangan.view']);
    Permission::create(['name' => 'ruangan.book']);
    Permission::create(['name' => 'ruangan.approve']);
    Permission::create(['name' => 'ruangan.manage']);

    Role::create(['name' => 'eksternal']);
    Role::create(['name' => 'subdit-aset']);
});

test('a user with ruangan.book can view and cancel only their own pending booking', function () {
    $owner = User::factory()->create();
    $owner->assignRole('eksternal');
    $owner->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $other = User::factory()->create();
    $other->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $booking = RoomBooking::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);

    expect($owner->can('view', $booking))->toBeTrue();
    expect($owner->can('cancel', $booking))->toBeTrue();

    expect($other->can('view', $booking))->toBeFalse();
    expect($other->can('cancel', $booking))->toBeFalse();
});

test('a booking can no longer be cancelled once it is no longer pending', function () {
    $owner = User::factory()->create();
    $owner->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $booking = RoomBooking::factory()->create(['user_id' => $owner->id, 'status' => 'approved']);

    expect($owner->can('cancel', $booking))->toBeFalse();
});

test('only ruangan.approve holders may review bookings; mitra (eksternal) cannot', function () {
    $mitra = User::factory()->create();
    $mitra->assignRole('eksternal');
    $mitra->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $approver = User::factory()->create();
    $approver->assignRole('subdit-aset');
    $approver->givePermissionTo(['ruangan.view', 'ruangan.approve', 'ruangan.manage']);

    $booking = RoomBooking::factory()->create(['status' => 'pending']);

    expect($mitra->can('review', $booking))->toBeFalse();
    expect($approver->can('review', $booking))->toBeTrue();
});

test('a booking already reviewed can no longer be reviewed again', function () {
    $approver = User::factory()->create();
    $approver->givePermissionTo(['ruangan.view', 'ruangan.approve']);

    $booking = RoomBooking::factory()->create(['status' => 'approved']);

    expect($approver->can('review', $booking))->toBeFalse();
});

test('only ruangan.manage holders can create, update, or delete rooms', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo(['ruangan.view', 'ruangan.manage']);

    $viewer = User::factory()->create();
    $viewer->givePermissionTo('ruangan.view');

    $room = Room::factory()->create();

    expect($manager->can('create', Room::class))->toBeTrue();
    expect($manager->can('update', $room))->toBeTrue();
    expect($manager->can('delete', $room))->toBeTrue();

    expect($viewer->can('create', Room::class))->toBeFalse();
    expect($viewer->can('update', $room))->toBeFalse();
    expect($viewer->can('delete', $room))->toBeFalse();
});
