<?php

use App\Models\Disposition;
use App\Models\Letter;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\Surat\DispositionReceived;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::create(['name' => 'surat.view']);
    Permission::create(['name' => 'surat.manage']);
    Permission::create(['name' => 'surat.dispose']);

    Role::create(['name' => 'direktur']);
    Role::create(['name' => 'deputi-transfer-teknologi']);
});

test('direktur can create the first disposition on any letter, which notifies the recipient and forwards the letter', function () {
    Notification::fake();

    $unit = Unit::factory()->create();
    $direktur = User::factory()->create();
    $direktur->assignRole('direktur');
    $direktur->givePermissionTo(['surat.view', 'surat.dispose']);

    $recipient = User::factory()->create(['unit_id' => $unit->id]);

    $letter = Letter::factory()->create(['status' => 'new']);

    $response = $this->actingAs($direktur)->post(route('surat.dispositions.store', $letter), [
        'to_unit_id' => $unit->id,
        'to_user_id' => $recipient->id,
        'instruction' => 'Mohon ditindaklanjuti.',
        'due_date' => now()->addWeek()->toDateString(),
    ]);

    $response->assertRedirect();

    expect($letter->fresh()->status)->toBe('forwarded');
    expect(Disposition::where('letter_id', $letter->id)->count())->toBe(1);

    Notification::assertSentTo($recipient, DispositionReceived::class);
});

test('a deputi without a prior disposition to their unit cannot dispose a letter', function () {
    $unit = Unit::factory()->create();
    $deputi = User::factory()->create(['unit_id' => $unit->id]);
    $deputi->assignRole('deputi-transfer-teknologi');
    $deputi->givePermissionTo(['surat.view', 'surat.dispose']);

    $letter = Letter::factory()->create();

    $response = $this->actingAs($deputi)->post(route('surat.dispositions.store', $letter), [
        'to_unit_id' => $unit->id,
        'instruction' => 'Mohon ditindaklanjuti.',
    ]);

    $response->assertForbidden();
    expect(Disposition::where('letter_id', $letter->id)->count())->toBe(0);
});

test('a deputi can create a disposisi lanjutan once the letter was already disposed to their unit', function () {
    $sourceUnit = Unit::factory()->create();
    $targetUnit = Unit::factory()->create();

    $deputi = User::factory()->create(['unit_id' => $sourceUnit->id]);
    $deputi->assignRole('deputi-transfer-teknologi');
    $deputi->givePermissionTo(['surat.view', 'surat.dispose']);

    $letter = Letter::factory()->create();
    Disposition::factory()->create([
        'letter_id' => $letter->id,
        'to_unit_id' => $sourceUnit->id,
        'status' => 'read',
    ]);

    $response = $this->actingAs($deputi)->post(route('surat.dispositions.store', $letter), [
        'to_unit_id' => $targetUnit->id,
        'instruction' => 'Mohon dikoordinasikan lebih lanjut.',
    ]);

    $response->assertRedirect();
    expect(Disposition::where('letter_id', $letter->id)->count())->toBe(2);
});

test('the letter becomes completed only once every disposition is completed', function () {
    $unitA = Unit::factory()->create();
    $unitB = Unit::factory()->create();

    $recipientA = User::factory()->create(['unit_id' => $unitA->id]);
    $recipientA->givePermissionTo('surat.view');
    $recipientB = User::factory()->create(['unit_id' => $unitB->id]);
    $recipientB->givePermissionTo('surat.view');

    $letter = Letter::factory()->create();
    $dispositionA = Disposition::factory()->create([
        'letter_id' => $letter->id,
        'to_unit_id' => $unitA->id,
        'to_user_id' => $recipientA->id,
        'status' => 'read',
    ]);
    $dispositionB = Disposition::factory()->create([
        'letter_id' => $letter->id,
        'to_unit_id' => $unitB->id,
        'to_user_id' => $recipientB->id,
        'status' => 'read',
    ]);
    $letter->update(['status' => 'forwarded']);

    $this->actingAs($recipientA)
        ->patch(route('surat.dispositions.update', [$letter, $dispositionA]), ['status' => 'completed'])
        ->assertRedirect();

    expect($letter->fresh()->status)->toBe('forwarded');

    $this->actingAs($recipientB)
        ->patch(route('surat.dispositions.update', [$letter, $dispositionB]), ['status' => 'completed'])
        ->assertRedirect();

    expect($letter->fresh()->status)->toBe('completed');
});

test('only the disposition recipient may update its status', function () {
    $unit = Unit::factory()->create();
    $recipient = User::factory()->create(['unit_id' => $unit->id]);
    $recipient->givePermissionTo('surat.view');

    $outsider = User::factory()->create();
    $outsider->givePermissionTo('surat.view');

    $letter = Letter::factory()->create();
    $disposition = Disposition::factory()->create([
        'letter_id' => $letter->id,
        'to_unit_id' => $unit->id,
        'to_user_id' => $recipient->id,
        'status' => 'read',
    ]);

    $this->actingAs($outsider)
        ->patch(route('surat.dispositions.update', [$letter, $disposition]), ['status' => 'completed'])
        ->assertForbidden();

    expect($disposition->fresh()->status)->toBe('read');
});

test('opening the letter detail marks the viewer disposition as read', function () {
    $unit = Unit::factory()->create();
    $recipient = User::factory()->create(['unit_id' => $unit->id]);
    $recipient->givePermissionTo('surat.view');

    $letter = Letter::factory()->create();
    $disposition = Disposition::factory()->create([
        'letter_id' => $letter->id,
        'to_unit_id' => $unit->id,
        'status' => 'new',
        'read_at' => null,
    ]);

    $this->actingAs($recipient)->get(route('surat.show', $letter))->assertOk();

    $disposition->refresh();
    expect($disposition->status)->toBe('read');
    expect($disposition->read_at)->not->toBeNull();
});

test('a user outside the disposition chain cannot view a confidential letter', function () {
    $outsider = User::factory()->create();
    $outsider->givePermissionTo('surat.view');

    $letter = Letter::factory()->create(['classification' => 'confidential']);

    $this->actingAs($outsider)->get(route('surat.show', $letter))->assertForbidden();
});
