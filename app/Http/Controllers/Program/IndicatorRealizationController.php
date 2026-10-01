<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Http\Requests\Program\StoreIndicatorRealizationRequest;
use App\Models\IndicatorRealization;
use App\Models\Program;
use App\Models\ProgramIndicator;
use App\Models\User;
use App\Notifications\Program\RealizationSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class IndicatorRealizationController extends Controller
{
    /**
     * Store (or resubmit a previously rejected) realization for the given indicator.
     */
    public function store(StoreIndicatorRealizationRequest $request, Program $program, ProgramIndicator $indicator): RedirectResponse
    {
        $period = $request->string('period')->toString();

        $existing = IndicatorRealization::query()
            ->where('indicator_id', $indicator->id)
            ->where('period', $period)
            ->first();

        $evidencePath = $existing?->evidence_path;

        if ($request->hasFile('evidence')) {
            if ($evidencePath) {
                Storage::disk('public')->delete($evidencePath);
            }

            $evidencePath = $request->file('evidence')->store('program/evidence', 'public');
        }

        $attributes = [
            'actual_value' => $request->input('actual_value'),
            'notes' => $request->string('notes')->toString() ?: null,
            'evidence_path' => $evidencePath,
            'reported_by' => $request->user()->id,
            'status' => 'submitted',
            'verified_by' => null,
            'verified_at' => null,
            'verification_notes' => null,
        ];

        $realization = $existing
            ? tap($existing)->update($attributes)
            : IndicatorRealization::create(['indicator_id' => $indicator->id, 'period' => $period, ...$attributes]);

        $verifiers = User::permission('program.verify')->get();

        Notification::send($verifiers, new RealizationSubmitted($realization));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Realisasi berhasil diajukan.']);

        return to_route('program.show', $program);
    }
}
