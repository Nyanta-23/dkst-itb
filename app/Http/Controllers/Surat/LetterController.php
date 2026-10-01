<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Surat\StoreLetterRequest;
use App\Http\Requests\Surat\UpdateLetterRequest;
use App\Models\Disposition;
use App\Models\Letter;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class LetterController extends Controller
{
    /**
     * Display a listing of the letters visible to the current user.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        Gate::authorize('viewAny', Letter::class);

        $tab = $request->string('tab')->toString() ?: 'all';
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();
        $classification = $request->string('classification')->toString();

        $query = Letter::query()->visibleTo($user)->with('creator:id,name');

        match ($tab) {
            'incoming' => $query->where('type', 'incoming'),
            'outgoing' => $query->where('type', 'outgoing'),
            'my-dispositions' => $query->whereHas(
                'dispositions',
                fn ($q) => $q->where('to_unit_id', $user->unit_id)->orWhere('to_user_id', $user->id)
            ),
            default => null,
        };

        if ($search !== '') {
            $query->where(fn ($q) => $q
                ->where('letter_number', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('sender', 'like', "%{$search}%"));
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($classification !== '') {
            $query->where('classification', $classification);
        }

        $letters = $query->latest('letter_date')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Letter $letter) => [
                'id' => $letter->id,
                'type' => $letter->type,
                'letterNumber' => $letter->letter_number,
                'subject' => $letter->subject,
                'sender' => $letter->sender,
                'recipient' => $letter->recipient,
                'letterDate' => $letter->letter_date->toDateString(),
                'classification' => $letter->classification,
                'status' => $letter->status,
                'creator' => $letter->creator->name,
            ]);

        return Inertia::render('surat/index', [
            'letters' => $letters,
            'filters' => [
                'tab' => $tab,
                'search' => $search,
                'status' => $status,
                'classification' => $classification,
            ],
            'can' => [
                'manage' => $user->can('surat.manage'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new letter.
     */
    public function create(): Response
    {
        Gate::authorize('create', Letter::class);

        return Inertia::render('surat/create');
    }

    /**
     * Store a newly created letter.
     */
    public function store(StoreLetterRequest $request): RedirectResponse
    {
        $path = $request->file('file')->store('letters', 'public');

        $letter = Letter::create([
            ...$request->safe()->except('file'),
            'file_path' => $path,
            'created_by' => $request->user()->id,
            'status' => 'new',
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Surat berhasil dicatat.']);

        return to_route('surat.show', $letter);
    }

    /**
     * Display the given letter, its disposition timeline, and mark any
     * disposition addressed to the viewing user as read.
     */
    public function show(Request $request, Letter $letter): Response
    {
        $user = $request->user();

        $letter->load([
            'creator:id,name',
            'dispositions' => fn ($query) => $query->orderBy('created_at'),
            'dispositions.fromUser:id,name',
            'dispositions.toUnit:id,name',
            'dispositions.toUser:id,name',
        ]);

        Gate::authorize('view', $letter);

        $this->markDispositionsReadFor($letter, $user);

        return Inertia::render('surat/show', [
            'letter' => [
                'id' => $letter->id,
                'type' => $letter->type,
                'letterNumber' => $letter->letter_number,
                'agendaNumber' => $letter->agenda_number,
                'subject' => $letter->subject,
                'sender' => $letter->sender,
                'recipient' => $letter->recipient,
                'letterDate' => $letter->letter_date->toDateString(),
                'receivedDate' => $letter->received_date?->toDateString(),
                'classification' => $letter->classification,
                'status' => $letter->status,
                'fileUrl' => $this->fileUrl($letter),
                'creator' => $letter->creator->name,
                'createdAt' => $letter->created_at->toIso8601String(),
            ],
            'dispositions' => $letter->dispositions->map(fn (Disposition $disposition) => [
                'id' => $disposition->id,
                'fromUser' => $disposition->fromUser->name,
                'toUnit' => $disposition->toUnit->name,
                'toUser' => $disposition->toUser?->name,
                'instruction' => $disposition->instruction,
                'dueDate' => $disposition->due_date?->toDateString(),
                'status' => $disposition->status,
                'readAt' => $disposition->read_at?->toIso8601String(),
                'createdAt' => $disposition->created_at->toIso8601String(),
                'canUpdateStatus' => $disposition->status !== 'completed' && Gate::allows('updateStatus', $disposition),
            ]),
            'units' => Unit::orderBy('name')->get(['id', 'name']),
            'usersByUnit' => User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'unit_id'])
                ->groupBy('unit_id'),
            'can' => [
                'manage' => $user->can('surat.manage'),
                'dispose' => Gate::allows('create', [Disposition::class, $letter]),
            ],
        ]);
    }

    /**
     * Show the form for editing the given letter.
     */
    public function edit(Letter $letter): Response
    {
        Gate::authorize('update', $letter);

        return Inertia::render('surat/edit', [
            'letter' => [
                'id' => $letter->id,
                'type' => $letter->type,
                'letterNumber' => $letter->letter_number,
                'agendaNumber' => $letter->agenda_number,
                'subject' => $letter->subject,
                'sender' => $letter->sender,
                'recipient' => $letter->recipient,
                'letterDate' => $letter->letter_date->toDateString(),
                'receivedDate' => $letter->received_date?->toDateString(),
                'classification' => $letter->classification,
                'fileUrl' => $this->fileUrl($letter),
            ],
        ]);
    }

    /**
     * Update the given letter.
     */
    public function update(UpdateLetterRequest $request, Letter $letter): RedirectResponse
    {
        $data = $request->safe()->except('file');

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($letter->file_path);
            $data['file_path'] = $request->file('file')->store('letters', 'public');
        }

        $letter->update($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Surat berhasil diperbarui.']);

        return to_route('surat.show', $letter);
    }

    /**
     * Remove the given letter.
     */
    public function destroy(Letter $letter): RedirectResponse
    {
        Gate::authorize('delete', $letter);

        $letter->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Surat berhasil dihapus.']);

        return to_route('surat.index');
    }

    /**
     * Get the public URL for the given letter's file, or null if it is missing on disk.
     */
    private function fileUrl(Letter $letter): ?string
    {
        return Storage::disk('public')->exists($letter->file_path)
            ? Storage::disk('public')->url($letter->file_path)
            : null;
    }

    /**
     * Mark any disposition in the given letter addressed to the user as read.
     */
    private function markDispositionsReadFor(Letter $letter, User $user): void
    {
        $letter->dispositions
            ->filter(fn (Disposition $disposition) => $disposition->status === 'new' && $disposition->isRecipient($user))
            ->each(fn (Disposition $disposition) => $disposition->update(['status' => 'read', 'read_at' => now()]));
    }
}
