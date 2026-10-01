<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Http\Requests\Program\ReviewIndicatorRealizationRequest;
use App\Models\IndicatorRealization;
use App\Notifications\Program\RealizationReviewed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class IndicatorRealizationVerificationController extends Controller
{
    /**
     * Display realizations awaiting, verified, or rejected by the current reviewer.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('program.verify');

        $tab = in_array($request->string('tab')->toString(), ['submitted', 'verified', 'rejected'], true)
            ? $request->string('tab')->toString()
            : 'submitted';

        $realizations = IndicatorRealization::query()
            ->where('status', $tab)
            ->with(['indicator.program:id,name,year,unit_id', 'indicator.program.unit:id,name', 'reporter:id,name', 'verifier:id,name'])
            ->orderByDesc($tab === 'submitted' ? 'created_at' : 'verified_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (IndicatorRealization $realization) => [
                'id' => $realization->id,
                'programId' => $realization->indicator->program_id,
                'program' => $realization->indicator->program->name,
                'unit' => $realization->indicator->program->unit->name,
                'indicator' => $realization->indicator->name,
                'period' => $realization->period,
                'actualValue' => (float) $realization->actual_value,
                'notes' => $realization->notes,
                'evidenceUrl' => $realization->evidence_path && Storage::disk('public')->exists($realization->evidence_path)
                    ? Storage::disk('public')->url($realization->evidence_path)
                    : null,
                'reporter' => $realization->reporter->name,
                'verifier' => $realization->verifier?->name,
                'verificationNotes' => $realization->verification_notes,
            ]);

        return Inertia::render('program/verifikasi', [
            'realizations' => $realizations,
            'tab' => $tab,
        ]);
    }

    /**
     * Verify or reject the given realization.
     */
    public function update(ReviewIndicatorRealizationRequest $request, IndicatorRealization $realization): RedirectResponse
    {
        $realization->update([
            'status' => $request->string('action')->toString(),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'verification_notes' => $request->string('verification_notes')->toString() ?: null,
        ]);

        $realization->reporter->notify(new RealizationReviewed($realization));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Keputusan verifikasi berhasil disimpan.']);

        return back();
    }
}
