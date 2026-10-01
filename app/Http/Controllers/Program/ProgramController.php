<?php

namespace App\Http\Controllers\Program;

use App\Exports\IkuExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Program\StoreProgramRequest;
use App\Http\Requests\Program\UpdateProgramRequest;
use App\Models\IndicatorRealization;
use App\Models\Program;
use App\Models\ProgramIndicator;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProgramController extends Controller
{
    /**
     * Display a listing of the programs visible to the current user.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        Gate::authorize('viewAny', Program::class);

        $year = $request->integer('year') ?: now()->year;
        $unitId = $request->integer('unit_id') ?: null;
        $status = $request->string('status')->toString();
        $search = trim($request->string('search')->toString());

        $query = Program::query()
            ->visibleTo($user)
            ->where('year', $year)
            ->with([
                'unit:id,name',
                'pic:id,name',
                'indicators.realizations' => fn ($q) => $q->where('status', 'verified'),
            ]);

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"));
        }

        $programs = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Program $program) => [
                'id' => $program->id,
                'code' => $program->code,
                'name' => $program->name,
                'unit' => $program->unit->name,
                'pic' => $program->pic?->name,
                'budget' => (float) $program->budget,
                'status' => $program->status,
                'progress' => $this->averageProgress($program),
            ]);

        return Inertia::render('program/index', [
            'programs' => $programs,
            'filters' => [
                'year' => $year,
                'unitId' => $unitId,
                'status' => $status,
                'search' => $search,
            ],
            'units' => Unit::orderBy('name')->get(['id', 'name']),
            'years' => Program::query()->visibleTo($user)->distinct()->orderByDesc('year')->pluck('year'),
            'can' => [
                'manage' => $user->can('program.manage'),
                'exportIku' => $user->can('iku.export'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new program.
     */
    public function create(Request $request): Response
    {
        Gate::authorize('create', Program::class);

        return Inertia::render('program/create', [
            'units' => Unit::orderBy('name')->get(['id', 'name']),
            'users' => User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'unit_id']),
        ]);
    }

    /**
     * Store a newly created program with its indicators.
     */
    public function store(StoreProgramRequest $request): RedirectResponse
    {
        $program = Program::create($request->safe()->except('indicators'));

        foreach ($request->array('indicators') as $indicator) {
            $program->indicators()->create($this->indicatorAttributes($indicator));
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Program berhasil ditambahkan.']);

        return to_route('program.show', $program);
    }

    /**
     * Display the given program and its indicator progress.
     */
    public function show(Request $request, Program $program): Response
    {
        $user = $request->user();

        Gate::authorize('view', $program);

        $program->load([
            'unit:id,name',
            'pic:id,name',
            'indicators.realizations' => fn ($q) => $q->with(['reporter:id,name', 'verifier:id,name'])->orderBy('period'),
        ]);

        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

        return Inertia::render('program/show', [
            'program' => [
                'id' => $program->id,
                'code' => $program->code,
                'name' => $program->name,
                'description' => $program->description,
                'unit' => $program->unit->name,
                'pic' => $program->pic?->name,
                'year' => $program->year,
                'budget' => (float) $program->budget,
                'startDate' => $program->start_date->toDateString(),
                'endDate' => $program->end_date?->toDateString(),
                'status' => $program->status,
            ],
            'indicators' => $program->indicators->map(fn (ProgramIndicator $indicator) => [
                'id' => $indicator->id,
                'name' => $indicator->name,
                'isIku' => $indicator->is_iku,
                'ikuCode' => $indicator->iku_code,
                'target' => (float) $indicator->target,
                'measurementUnit' => $indicator->measurement_unit,
                'achieved' => $this->verifiedTotal($indicator),
                'percentage' => $this->indicatorPercentage($indicator),
                'canReport' => Gate::allows('create', [IndicatorRealization::class, $indicator]),
                'periods' => collect($quarters)->map(function (string $quarter) use ($indicator, $program) {
                    $period = "{$program->year}-{$quarter}";
                    $realization = $indicator->realizations->firstWhere('period', $period);

                    return [
                        'period' => $period,
                        'quarter' => $quarter,
                        'realization' => $realization ? [
                            'id' => $realization->id,
                            'actualValue' => (float) $realization->actual_value,
                            'notes' => $realization->notes,
                            'evidenceUrl' => $realization->evidence_path && Storage::disk('public')->exists($realization->evidence_path)
                                ? Storage::disk('public')->url($realization->evidence_path)
                                : null,
                            'status' => $realization->status,
                            'reporter' => $realization->reporter->name,
                            'verifier' => $realization->verifier?->name,
                            'verificationNotes' => $realization->verification_notes,
                        ] : null,
                    ];
                }),
            ]),
            'can' => [
                'manage' => $user->can('program.manage'),
            ],
        ]);
    }

    /**
     * Show the form for editing the given program.
     */
    public function edit(Program $program): Response
    {
        Gate::authorize('update', $program);

        $program->load('indicators');

        return Inertia::render('program/edit', [
            'program' => [
                'id' => $program->id,
                'unitId' => $program->unit_id,
                'picId' => $program->pic_id,
                'code' => $program->code,
                'name' => $program->name,
                'description' => $program->description,
                'year' => $program->year,
                'budget' => (float) $program->budget,
                'startDate' => $program->start_date->toDateString(),
                'endDate' => $program->end_date?->toDateString(),
                'status' => $program->status,
                'indicators' => $program->indicators->map(fn (ProgramIndicator $indicator) => [
                    'id' => $indicator->id,
                    'name' => $indicator->name,
                    'isIku' => $indicator->is_iku,
                    'ikuCode' => $indicator->iku_code,
                    'target' => (float) $indicator->target,
                    'measurementUnit' => $indicator->measurement_unit,
                ]),
            ],
            'units' => Unit::orderBy('name')->get(['id', 'name']),
            'users' => User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'unit_id']),
        ]);
    }

    /**
     * Update the given program and sync its indicators.
     */
    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update($request->safe()->except('indicators'));

        $submitted = collect($request->array('indicators'));
        $keepIds = $submitted->pluck('id')->filter()->all();

        $program->indicators()->whereNotIn('id', $keepIds)->delete();

        foreach ($submitted as $indicator) {
            $attributes = $this->indicatorAttributes($indicator);

            if (! empty($indicator['id'])) {
                $program->indicators()->whereKey($indicator['id'])->update($attributes);
            } else {
                $program->indicators()->create($attributes);
            }
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Program berhasil diperbarui.']);

        return to_route('program.show', $program);
    }

    /**
     * Export every IKU indicator for the given year to an Excel file.
     */
    public function exportIku(Request $request): BinaryFileResponse
    {
        Gate::authorize('iku.export');

        $year = $request->integer('year') ?: now()->year;

        return Excel::download(new IkuExport($year), "iku-{$year}.xlsx");
    }

    /**
     * @param  array<string, mixed>  $indicator
     * @return array<string, mixed>
     */
    private function indicatorAttributes(array $indicator): array
    {
        $isIku = (bool) ($indicator['is_iku'] ?? false);

        return [
            'name' => $indicator['name'],
            'is_iku' => $isIku,
            'iku_code' => $isIku ? ($indicator['iku_code'] ?? null) : null,
            'target' => $indicator['target'],
            'measurement_unit' => $indicator['measurement_unit'],
        ];
    }

    /**
     * Sum of verified realization values for the given indicator.
     */
    private function verifiedTotal(ProgramIndicator $indicator): float
    {
        return (float) $indicator->realizations
            ->where('status', 'verified')
            ->sum('actual_value');
    }

    /**
     * Percentage of target achieved via verified realizations, capped at 100.
     */
    private function indicatorPercentage(ProgramIndicator $indicator): float
    {
        $target = (float) $indicator->target;

        if ($target <= 0) {
            return 0.0;
        }

        return min(100.0, round(($this->verifiedTotal($indicator) / $target) * 100, 1));
    }

    /**
     * Average indicator-completion percentage for the given program.
     */
    private function averageProgress(Program $program): float
    {
        if ($program->indicators->isEmpty()) {
            return 0.0;
        }

        return round($program->indicators->avg(fn (ProgramIndicator $indicator) => $this->indicatorPercentage($indicator)), 1);
    }
}
