<?php

use App\Models\Disposition;
use App\Models\Letter;
use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::create(['name' => 'surat.view']);
    Permission::create(['name' => 'surat.manage']);
    Permission::create(['name' => 'surat.dispose']);

    Role::create(['name' => 'direktur']);
    Role::create(['name' => 'sekretariat']);
    Role::create(['name' => 'super-admin']);
});

test('sekretariat sees every letter including confidential ones', function () {
    $sekretariat = User::factory()->create();
    $sekretariat->assignRole('sekretariat');
    $sekretariat->givePermissionTo(['surat.view', 'surat.manage']);

    $letter = Letter::factory()->create(['classification' => 'confidential']);

    expect(Letter::visibleTo($sekretariat)->count())->toBe(1);
    expect($sekretariat->can('view', $letter))->toBeTrue();
});

test('direktur sees every letter regardless of disposition', function () {
    $direktur = User::factory()->create();
    $direktur->assignRole('direktur');
    $direktur->givePermissionTo('surat.view');

    Letter::factory()->count(3)->create();

    expect(Letter::visibleTo($direktur)->count())->toBe(3);
});

test('a plain user only sees letters disposed to their unit', function () {
    $unitA = Unit::factory()->create();
    $unitB = Unit::factory()->create();

    $staf = User::factory()->create(['unit_id' => $unitA->id]);
    $staf->givePermissionTo('surat.view');

    $letterForMyUnit = Letter::factory()->create();
    Disposition::factory()->create(['letter_id' => $letterForMyUnit->id, 'to_unit_id' => $unitA->id]);

    $letterForOtherUnit = Letter::factory()->create();
    Disposition::factory()->create(['letter_id' => $letterForOtherUnit->id, 'to_unit_id' => $unitB->id]);

    Letter::factory()->create(); // never disposed to anyone

    $visible = Letter::visibleTo($staf)->get();

    expect($visible)->toHaveCount(1);
    expect($visible->first()->id)->toBe($letterForMyUnit->id);
    expect($staf->can('view', $letterForOtherUnit))->toBeFalse();
});

test('a plain user sees a letter disposed directly to them even outside their unit', function () {
    $unitA = Unit::factory()->create();
    $unitB = Unit::factory()->create();

    $user = User::factory()->create(['unit_id' => $unitA->id]);
    $user->givePermissionTo('surat.view');

    $letter = Letter::factory()->create();
    Disposition::factory()->create([
        'letter_id' => $letter->id,
        'to_unit_id' => $unitB->id,
        'to_user_id' => $user->id,
    ]);

    expect($user->can('view', $letter))->toBeTrue();
});

test('a disposition recipient can view a confidential letter addressed to them', function () {
    $unit = Unit::factory()->create();
    $recipient = User::factory()->create(['unit_id' => $unit->id]);
    $recipient->givePermissionTo('surat.view');

    $letter = Letter::factory()->create(['classification' => 'confidential']);
    Disposition::factory()->create(['letter_id' => $letter->id, 'to_unit_id' => $unit->id]);

    expect($recipient->can('view', $letter))->toBeTrue();
});

test('only surat.manage holders can create, update, or delete letters', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo(['surat.view', 'surat.manage']);

    $viewer = User::factory()->create();
    $viewer->givePermissionTo('surat.view');

    $letter = Letter::factory()->create();

    expect($manager->can('create', Letter::class))->toBeTrue();
    expect($manager->can('update', $letter))->toBeTrue();
    expect($manager->can('delete', $letter))->toBeTrue();

    expect($viewer->can('create', Letter::class))->toBeFalse();
    expect($viewer->can('update', $letter))->toBeFalse();
    expect($viewer->can('delete', $letter))->toBeFalse();
});
