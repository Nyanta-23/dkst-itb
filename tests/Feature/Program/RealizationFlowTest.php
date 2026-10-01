<?php

use App\Models\IndicatorRealization;
use App\Models\Program;
use App\Models\ProgramIndicator;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\Program\RealizationReviewed;
use App\Notifications\Program\RealizationSubmitted;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Permission::create(['name' => 'program.view']);
    Permission::create(['name' => 'program.manage']);
    Permission::create(['name' => 'program.verify']);
    Permission::create(['name' => 'program.report']);
});

test('a reporter can submit a realization for an indicator in their own unit, which notifies every verifier', function () {
    Notification::fake();

    $unit = Unit::factory()->create();
    $program = Program::factory()->create(['unit_id' => $unit->id, 'year' => now()->year]);
    $indicator = ProgramIndicator::factory()->create(['program_id' => $program->id, 'target' => 10]);

    $reporter = User::factory()->create(['unit_id' => $unit->id]);
    $reporter->givePermissionTo(['program.view', 'program.report']);

    $verifier = User::factory()->create();
    $verifier->givePermissionTo(['program.view', 'program.verify']);

    $period = now()->year.'-Q1';

    $response = $this->actingAs($reporter)->post(
        route('program.indicators.realizations.store', [$program, $indicator]),
        ['period' => $period, 'actual_value' => 5, 'notes' => 'Realisasi awal']
    );

    $response->assertRedirect();

    $realization = IndicatorRealization::sole();
    expect($realization->status)->toBe('submitted');
    expect($realization->period)->toBe($period);

    Notification::assertSentTo($verifier, RealizationSubmitted::class);
});

test('a reporter cannot submit a realization for an indicator outside their own unit', function () {
    $program = Program::factory()->create(['unit_id' => Unit::factory(), 'year' => now()->year]);
    $indicator = ProgramIndicator::factory()->create(['program_id' => $program->id]);

    $outsider = User::factory()->create(['unit_id' => Unit::factory()]);
    $outsider->givePermissionTo(['program.view', 'program.report']);

    $response = $this->actingAs($outsider)->post(
        route('program.indicators.realizations.store', [$program, $indicator]),
        ['period' => now()->year.'-Q1', 'actual_value' => 5]
    );

    $response->assertForbidden();
    expect(IndicatorRealization::count())->toBe(0);
});

test('a realization cannot be submitted twice for the same indicator and period unless rejected', function () {
    $unit = Unit::factory()->create();
    $program = Program::factory()->create(['unit_id' => $unit->id, 'year' => now()->year]);
    $indicator = ProgramIndicator::factory()->create(['program_id' => $program->id]);
    $reporter = User::factory()->create(['unit_id' => $unit->id]);
    $reporter->givePermissionTo(['program.view', 'program.report']);

    $period = now()->year.'-Q1';

    IndicatorRealization::factory()->create([
        'indicator_id' => $indicator->id,
        'period' => $period,
        'status' => 'submitted',
    ]);

    $response = $this->actingAs($reporter)->post(
        route('program.indicators.realizations.store', [$program, $indicator]),
        ['period' => $period, 'actual_value' => 8]
    );

    $response->assertSessionHasErrors('period');
    expect(IndicatorRealization::count())->toBe(1);
});

test('a rejected realization can be corrected and resubmitted, returning it to submitted', function () {
    $unit = Unit::factory()->create();
    $program = Program::factory()->create(['unit_id' => $unit->id, 'year' => now()->year]);
    $indicator = ProgramIndicator::factory()->create(['program_id' => $program->id]);
    $reporter = User::factory()->create(['unit_id' => $unit->id]);
    $reporter->givePermissionTo(['program.view', 'program.report']);

    $period = now()->year.'-Q1';

    $realization = IndicatorRealization::factory()->create([
        'indicator_id' => $indicator->id,
        'period' => $period,
        'status' => 'rejected',
        'verification_notes' => 'Data tidak sesuai bukti.',
        'verified_by' => User::factory(),
        'verified_at' => now(),
    ]);

    $response = $this->actingAs($reporter)->post(
        route('program.indicators.realizations.store', [$program, $indicator]),
        ['period' => $period, 'actual_value' => 12, 'notes' => 'Sudah diperbaiki']
    );

    $response->assertRedirect();

    $realization->refresh();
    expect($realization->status)->toBe('submitted');
    expect((float) $realization->actual_value)->toBe(12.0);
    expect($realization->verification_notes)->toBeNull();
    expect($realization->verified_by)->toBeNull();
    expect(IndicatorRealization::count())->toBe(1);
});

test('verifying a realization notifies the reporter and raises the indicator percentage', function () {
    Notification::fake();

    $program = Program::factory()->create(['year' => now()->year]);
    $indicator = ProgramIndicator::factory()->create(['program_id' => $program->id, 'target' => 10]);
    $reporter = User::factory()->create();

    $verifier = User::factory()->create();
    $verifier->givePermissionTo(['program.view', 'program.verify']);

    $realization = IndicatorRealization::factory()->create([
        'indicator_id' => $indicator->id,
        'period' => now()->year.'-Q1',
        'actual_value' => 5,
        'reported_by' => $reporter->id,
        'status' => 'submitted',
    ]);

    $response = $this->actingAs($verifier)->patch(route('program.verifikasi.update', $realization), [
        'action' => 'verified',
    ]);

    $response->assertRedirect();

    $realization->refresh();
    expect($realization->status)->toBe('verified');
    expect($realization->verified_by)->toBe($verifier->id);

    Notification::assertSentTo($reporter, RealizationReviewed::class);

    $manager = User::factory()->create();
    $manager->givePermissionTo(['program.view', 'program.manage']);

    $show = $this->actingAs($manager)->get(route('program.show', $program));
    $show->assertOk();
    $show->assertInertia(fn ($page) => $page
        ->where('indicators.0.achieved', 5)
        ->where('indicators.0.percentage', 50));
});

test('rejecting a realization requires a reason', function () {
    $verifier = User::factory()->create();
    $verifier->givePermissionTo(['program.view', 'program.verify']);

    $realization = IndicatorRealization::factory()->create(['status' => 'submitted']);

    $this->actingAs($verifier)
        ->patch(route('program.verifikasi.update', $realization), ['action' => 'rejected'])
        ->assertSessionHasErrors('verification_notes');

    $response = $this->actingAs($verifier)->patch(route('program.verifikasi.update', $realization), [
        'action' => 'rejected',
        'verification_notes' => 'Bukti tidak sesuai.',
    ]);

    $response->assertRedirect();
    expect($realization->fresh()->status)->toBe('rejected');
    expect($realization->fresh()->verification_notes)->toBe('Bukti tidak sesuai.');
});

test('a realization that is not pending can no longer be reviewed', function () {
    $verifier = User::factory()->create();
    $verifier->givePermissionTo(['program.view', 'program.verify']);

    $realization = IndicatorRealization::factory()->create(['status' => 'verified']);

    $this->actingAs($verifier)
        ->patch(route('program.verifikasi.update', $realization), ['action' => 'verified'])
        ->assertForbidden();
});
