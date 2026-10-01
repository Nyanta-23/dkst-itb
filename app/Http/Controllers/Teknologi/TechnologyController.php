<?php

namespace App\Http\Controllers\Teknologi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teknologi\StoreTechnologyRequest;
use App\Http\Requests\Teknologi\UpdateTechnologyRequest;
use App\Models\Deal;
use App\Models\IpAsset;
use App\Models\Partner;
use App\Models\Technology;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TechnologyController extends Controller
{
    /**
     * Display a listing of technologies (table or pipeline, toggled client-side).
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Technology::class);

        $search = trim($request->string('search')->toString());
        $sector = $request->string('sector')->toString();
        $trl = $request->integer('trl') ?: null;
        $status = $request->string('status')->toString();

        $query = Technology::query()->with('pic:id,name');

        if ($search !== '') {
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('inventors', 'like', "%{$search}%"));
        }

        if ($sector !== '') {
            $query->where('sector', $sector);
        }

        if ($trl) {
            $query->where('trl', $trl);
        }

        if ($status !== '') {
            $query->where('commercialization_status', $status);
        }

        $technologies = $query->withCount('ipAssets')
            ->orderBy('title')
            ->get()
            ->map(fn (Technology $technology) => [
                'id' => $technology->id,
                'code' => $technology->code,
                'title' => $technology->title,
                'sector' => $technology->sector,
                'faculty' => $technology->faculty,
                'trl' => $technology->trl,
                'ipAssetsCount' => $technology->ip_assets_count,
                'commercializationStatus' => $technology->commercialization_status,
                'pic' => $technology->pic?->name,
            ]);

        return Inertia::render('teknologi/index', [
            'technologies' => $technologies,
            'filters' => [
                'search' => $search,
                'sector' => $sector,
                'trl' => $trl,
                'status' => $status,
            ],
            'sectors' => Technology::query()->whereNotNull('sector')->distinct()->orderBy('sector')->pluck('sector'),
            'summary' => $this->summary(),
            'can' => [
                'manage' => $request->user()->can('teknologi.manage'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new technology.
     */
    public function create(Request $request): Response
    {
        Gate::authorize('create', Technology::class);

        return Inertia::render('teknologi/create', [
            'pics' => $this->dttUsers(),
        ]);
    }

    /**
     * Store a newly created technology.
     */
    public function store(StoreTechnologyRequest $request): RedirectResponse
    {
        $technology = Technology::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Teknologi berhasil ditambahkan.']);

        return to_route('teknologi.show', $technology);
    }

    /**
     * Display the given technology with its IP assets and deals.
     */
    public function show(Request $request, Technology $technology): Response
    {
        Gate::authorize('view', $technology);

        $technology->load([
            'pic:id,name',
            'ipAssets' => fn ($q) => $q->orderByDesc('created_at'),
            'deals' => fn ($q) => $q->with('partner:id,name')->orderByDesc('start_date'),
        ]);

        return Inertia::render('teknologi/show', [
            'technology' => [
                'id' => $technology->id,
                'code' => $technology->code,
                'title' => $technology->title,
                'description' => $technology->description,
                'sector' => $technology->sector,
                'faculty' => $technology->faculty,
                'inventors' => $technology->inventors,
                'trl' => $technology->trl,
                'commercializationStatus' => $technology->commercialization_status,
                'pic' => $technology->pic?->name,
            ],
            'ipAssets' => $technology->ipAssets->map(fn (IpAsset $ipAsset) => [
                'id' => $ipAsset->id,
                'type' => $ipAsset->type,
                'title' => $ipAsset->title,
                'applicationNumber' => $ipAsset->application_number,
                'certificateNumber' => $ipAsset->certificate_number,
                'filingDate' => $ipAsset->filing_date?->toDateString(),
                'issueDate' => $ipAsset->issue_date?->toDateString(),
                'status' => $ipAsset->status,
            ]),
            'deals' => $technology->deals->map(fn (Deal $deal) => [
                'id' => $deal->id,
                'partnerId' => $deal->partner_id,
                'partner' => $deal->partner->name,
                'type' => $deal->type,
                'value' => (float) $deal->value,
                'startDate' => $deal->start_date->toDateString(),
                'endDate' => $deal->end_date?->toDateString(),
                'status' => $deal->status,
            ]),
            'partners' => Partner::orderBy('name')->get(['id', 'name']),
            'can' => [
                'manage' => $request->user()->can('teknologi.manage'),
            ],
        ]);
    }

    /**
     * Show the form for editing the given technology.
     */
    public function edit(Technology $technology): Response
    {
        Gate::authorize('update', $technology);

        return Inertia::render('teknologi/edit', [
            'technology' => [
                'id' => $technology->id,
                'code' => $technology->code,
                'title' => $technology->title,
                'description' => $technology->description,
                'sector' => $technology->sector,
                'faculty' => $technology->faculty,
                'inventors' => $technology->inventors,
                'trl' => $technology->trl,
                'commercializationStatus' => $technology->commercialization_status,
                'picId' => $technology->pic_id,
            ],
            'pics' => $this->dttUsers(),
        ]);
    }

    /**
     * Update the given technology.
     */
    public function update(UpdateTechnologyRequest $request, Technology $technology): RedirectResponse
    {
        $technology->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Teknologi berhasil diperbarui.']);

        return to_route('teknologi.show', $technology);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function dttUsers()
    {
        $dttUnitId = Unit::where('code', 'DTT')->value('id');

        return User::query()
            ->where('unit_id', $dttUnitId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(): array
    {
        return [
            'byStatus' => Technology::query()
                ->selectRaw('commercialization_status, count(*) as total')
                ->groupBy('commercialization_status')
                ->pluck('total', 'commercialization_status'),
            'ipAssetsGranted' => IpAsset::where('status', 'granted')->count(),
            'activeDealsValue' => (float) Deal::where('status', 'active')->sum('value'),
        ];
    }
}
