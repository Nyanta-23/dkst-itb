<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Surat\StoreDispositionRequest;
use App\Http\Requests\Surat\UpdateDispositionStatusRequest;
use App\Models\Disposition;
use App\Models\Letter;
use App\Models\User;
use App\Notifications\Surat\DispositionReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class DispositionController extends Controller
{
    /**
     * Store a newly created disposition for the given letter.
     */
    public function store(StoreDispositionRequest $request, Letter $letter): RedirectResponse
    {
        $disposition = $letter->dispositions()->create([
            ...$request->validated(),
            'from_user_id' => $request->user()->id,
            'status' => 'new',
        ]);

        $letter->syncStatusFromDispositions();

        $recipients = $disposition->to_user_id
            ? User::where('id', $disposition->to_user_id)->get()
            : User::where('unit_id', $disposition->to_unit_id)->get();

        Notification::send($recipients, new DispositionReceived($disposition));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Disposisi berhasil dibuat.']);

        return back();
    }

    /**
     * Update the status of the given disposition (menandai diproses/selesai).
     */
    public function update(UpdateDispositionStatusRequest $request, Letter $letter, Disposition $disposition): RedirectResponse
    {
        $disposition->update(['status' => $request->string('status')->toString()]);

        $letter->syncStatusFromDispositions();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Status disposisi berhasil diperbarui.']);

        return back();
    }
}
