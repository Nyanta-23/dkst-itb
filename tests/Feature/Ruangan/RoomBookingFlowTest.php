<?php

use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use App\Notifications\Ruangan\BookingReviewed;
use App\Notifications\Ruangan\BookingSubmitted;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Permission::create(['name' => 'ruangan.view']);
    Permission::create(['name' => 'ruangan.book']);
    Permission::create(['name' => 'ruangan.approve']);
    Permission::create(['name' => 'ruangan.manage']);
});

test('submitting a booking notifies every approver and leaves it pending', function () {
    Notification::fake();

    $room = Room::factory()->create(['capacity' => 10, 'is_active' => true]);
    $applicant = User::factory()->create();
    $applicant->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $approver = User::factory()->create();
    $approver->givePermissionTo(['ruangan.view', 'ruangan.approve']);

    $response = $this->actingAs($applicant)->post(route('ruangan.booking.store'), [
        'room_id' => $room->id,
        'booking_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'purpose' => 'Rapat tim',
        'participant_count' => 5,
    ]);

    $response->assertRedirect();

    $booking = RoomBooking::sole();
    expect($booking->status)->toBe('pending');

    Notification::assertSentTo($approver, BookingSubmitted::class);
});

test('a booking is rejected when participant count exceeds room capacity', function () {
    $room = Room::factory()->create(['capacity' => 5]);
    $applicant = User::factory()->create();
    $applicant->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $response = $this->actingAs($applicant)->post(route('ruangan.booking.store'), [
        'room_id' => $room->id,
        'booking_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'purpose' => 'Rapat tim besar',
        'participant_count' => 20,
    ]);

    $response->assertSessionHasErrors('participant_count');
    expect(RoomBooking::count())->toBe(0);
});

test('a booking is rejected when it conflicts with an existing pending or approved booking', function () {
    $room = Room::factory()->create(['capacity' => 10]);
    $applicant = User::factory()->create();
    $applicant->givePermissionTo(['ruangan.view', 'ruangan.book']);

    RoomBooking::factory()->create([
        'room_id' => $room->id,
        'booking_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'status' => 'approved',
    ]);

    $response = $this->actingAs($applicant)->post(route('ruangan.booking.store'), [
        'room_id' => $room->id,
        'booking_date' => now()->addDay()->toDateString(),
        'start_time' => '09:30',
        'end_time' => '10:30',
        'purpose' => 'Rapat lain',
        'participant_count' => 3,
    ]);

    $response->assertSessionHasErrors('start_time');
    expect(RoomBooking::count())->toBe(1);
});

test('an inactive room cannot be booked', function () {
    $room = Room::factory()->create(['is_active' => false, 'capacity' => 10]);
    $applicant = User::factory()->create();
    $applicant->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $response = $this->actingAs($applicant)->post(route('ruangan.booking.store'), [
        'room_id' => $room->id,
        'booking_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'purpose' => 'Rapat tim',
        'participant_count' => 3,
    ]);

    $response->assertSessionHasErrors('room_id');
    expect(RoomBooking::count())->toBe(0);
});

test('approving a booking notifies the applicant and records who approved it', function () {
    Notification::fake();

    $applicant = User::factory()->create();
    $applicant->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $approver = User::factory()->create();
    $approver->givePermissionTo(['ruangan.view', 'ruangan.approve']);

    $booking = RoomBooking::factory()->create(['user_id' => $applicant->id, 'status' => 'pending']);

    $response = $this->actingAs($approver)->patch(route('ruangan.approvals.update', $booking), [
        'action' => 'approved',
    ]);

    $response->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe('approved');
    expect($booking->approved_by)->toBe($approver->id);
    expect($booking->approved_at)->not->toBeNull();

    Notification::assertSentTo($applicant, BookingReviewed::class);
});

test('approving a booking fails when it conflicts with an already approved booking', function () {
    $room = Room::factory()->create(['capacity' => 10]);
    $approver = User::factory()->create();
    $approver->givePermissionTo(['ruangan.view', 'ruangan.approve']);

    RoomBooking::factory()->create([
        'room_id' => $room->id,
        'booking_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'status' => 'approved',
    ]);

    $pending = RoomBooking::factory()->create([
        'room_id' => $room->id,
        'booking_date' => now()->addDay()->toDateString(),
        'start_time' => '09:30',
        'end_time' => '10:30',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($approver)->patch(route('ruangan.approvals.update', $pending), [
        'action' => 'approved',
    ]);

    $response->assertSessionHasErrors('action');
    expect($pending->fresh()->status)->toBe('pending');
});

test('rejecting a booking requires a reason', function () {
    $approver = User::factory()->create();
    $approver->givePermissionTo(['ruangan.view', 'ruangan.approve']);

    $booking = RoomBooking::factory()->create(['status' => 'pending']);

    $this->actingAs($approver)
        ->patch(route('ruangan.approvals.update', $booking), ['action' => 'rejected'])
        ->assertSessionHasErrors('approval_notes');

    $response = $this->actingAs($approver)->patch(route('ruangan.approvals.update', $booking), [
        'action' => 'rejected',
        'approval_notes' => 'Ruangan sedang direnovasi.',
    ]);

    $response->assertRedirect();
    expect($booking->fresh()->status)->toBe('rejected');
    expect($booking->fresh()->approval_notes)->toBe('Ruangan sedang direnovasi.');
});

test('a user can cancel their own pending booking but not someone else\'s', function () {
    $owner = User::factory()->create();
    $owner->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $other = User::factory()->create();
    $other->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $booking = RoomBooking::factory()->create(['user_id' => $owner->id, 'status' => 'pending']);

    $this->actingAs($other)
        ->patch(route('ruangan.booking.cancel', $booking))
        ->assertForbidden();

    $this->actingAs($owner)
        ->patch(route('ruangan.booking.cancel', $booking))
        ->assertRedirect();

    expect($booking->fresh()->status)->toBe('cancelled');
});

test('a mitra (eksternal) user cannot access the approvals page', function () {
    $mitra = User::factory()->create();
    $mitra->givePermissionTo(['ruangan.view', 'ruangan.book']);

    $this->actingAs($mitra)->get(route('ruangan.approvals.index'))->assertForbidden();
});
