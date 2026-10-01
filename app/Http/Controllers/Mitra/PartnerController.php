<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\StorePartnerRequest;
use App\Http\Requests\Mitra\UpdatePartnerRequest;
use App\Models\Deal;
use App\Models\Partner;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PartnerController extends Controller
{
    /**
     * Display a listing of partners.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Partner::class);

        $search = trim($request->string('search')->toString());
        $type = $request->string('type')->toString();

        $query = Partner::query();

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($type !== '') {
            $query->where('type', $type);
        }

        $partners = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Partner $partner) => [
                'id' => $partner->id,
                'name' => $partner->name,
                'type' => $partner->type,
                'sector' => $partner->sector,
                'contactPerson' => $partner->contact_person,
                'email' => $partner->email,
                'phone' => $partner->phone,
            ]);

        return Inertia::render('mitra/index', [
            'partners' => $partners,
            'filters' => [
                'search' => $search,
                'type' => $type,
            ],
            'can' => [
                'manage' => $request->user()->can('mitra.manage'),
            ],
        ]);
    }

    /**
     * Store a newly created partner.
     */
    public function store(StorePartnerRequest $request): RedirectResponse
    {
        Partner::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Mitra berhasil ditambahkan.']);

        return back();
    }

    /**
     * Display the given partner, its deals, and connected tenants.
     */
    public function show(Request $request, Partner $partner): Response
    {
        Gate::authorize('view', $partner);

        $partner->load([
            'deals' => fn ($q) => $q->with('technology:id,code,title')->orderByDesc('start_date'),
            'tenants' => fn ($q) => $q->orderBy('startup_name'),
        ]);

        return Inertia::render('mitra/show', [
            'partner' => [
                'id' => $partner->id,
                'name' => $partner->name,
                'type' => $partner->type,
                'sector' => $partner->sector,
                'address' => $partner->address,
                'contactPerson' => $partner->contact_person,
                'email' => $partner->email,
                'phone' => $partner->phone,
            ],
            'deals' => $partner->deals->map(fn (Deal $deal) => [
                'id' => $deal->id,
                'technologyId' => $deal->technology_id,
                'technology' => $deal->technology?->title,
                'type' => $deal->type,
                'value' => (float) $deal->value,
                'startDate' => $deal->start_date->toDateString(),
                'endDate' => $deal->end_date?->toDateString(),
                'status' => $deal->status,
            ]),
            'tenants' => $partner->tenants->map(fn (Tenant $tenant) => [
                'id' => $tenant->id,
                'startupName' => $tenant->startup_name,
                'stage' => $tenant->stage,
                'status' => $tenant->status,
            ]),
            'hasActiveDeal' => $partner->hasActiveDeal(),
            'can' => [
                'manage' => $request->user()->can('mitra.manage'),
            ],
        ]);
    }

    /**
     * Update the given partner.
     */
    public function update(UpdatePartnerRequest $request, Partner $partner): RedirectResponse
    {
        $partner->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Mitra berhasil diperbarui.']);

        return back();
    }

    /**
     * Remove the given partner, unless it still has an active deal.
     */
    public function destroy(Partner $partner): RedirectResponse
    {
        Gate::authorize('delete', $partner);

        if ($partner->hasActiveDeal()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Mitra ini masih memiliki kerja sama aktif dan tidak dapat dihapus.']);

            return back();
        }

        $partner->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Mitra berhasil dihapus.']);

        return to_route('mitra.index');
    }
}
