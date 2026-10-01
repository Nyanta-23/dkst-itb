<?php

use App\Models\Program;
use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::create(['name' => 'program.view']);
    Permission::create(['name' => 'program.manage']);
    Permission::create(['name' => 'program.verify']);
    Permission::create(['name' => 'program.report']);

    Role::create(['name' => 'direktur']);
    Role::create(['name' => 'subdit-keuangan']);
});

test('program.manage holders see every program regardless of unit', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo(['program.view', 'program.manage']);

    Program::factory()->count(3)->create();

    expect(Program::visibleTo($manager)->count())->toBe(3);
});

test('direktur and subdit-keuangan see every program', function () {
    $direktur = User::factory()->create();
    $direktur->assignRole('direktur');
    $direktur->givePermissionTo('program.view');

    $keuangan = User::factory()->create();
    $keuangan->assignRole('subdit-keuangan');
    $keuangan->givePermissionTo('program.view');

    Program::factory()->count(2)->create();

    expect(Program::visibleTo($direktur)->count())->toBe(2);
    expect(Program::visibleTo($keuangan)->count())->toBe(2);
});

test('a plain unit user only sees programs belonging to their own unit', function () {
    $unitA = Unit::factory()->create();
    $unitB = Unit::factory()->create();

    $staf = User::factory()->create(['unit_id' => $unitA->id]);
    $staf->givePermissionTo('program.view');

    $ownProgram = Program::factory()->create(['unit_id' => $unitA->id]);
    Program::factory()->create(['unit_id' => $unitB->id]);

    $visible = Program::visibleTo($staf)->get();

    expect($visible)->toHaveCount(1);
    expect($visible->first()->id)->toBe($ownProgram->id);
    expect($staf->can('view', $ownProgram))->toBeTrue();
});

test('only program.manage holders can create or update programs', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo(['program.view', 'program.manage']);

    $viewer = User::factory()->create();
    $viewer->givePermissionTo('program.view');

    $program = Program::factory()->create();

    expect($manager->can('create', Program::class))->toBeTrue();
    expect($manager->can('update', $program))->toBeTrue();

    expect($viewer->can('create', Program::class))->toBeFalse();
    expect($viewer->can('update', $program))->toBeFalse();
});
