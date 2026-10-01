<?php

namespace App\Http\Controllers;

use App\Models\Disposition;
use App\Models\IndicatorRealization;
use App\Models\IpAsset;
use App\Models\Letter;
use App\Models\Partner;
use App\Models\Program;
use App\Models\ProgramIndicator;
use App\Models\RoomBooking;
use App\Models\Technology;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $showMyTasks = $user->can('program.verify')
            || ($user->unit_id && ($user->can('surat.view') || $user->can('program.report')));

        return Inertia::render('dashboard', [
            'stats' => $this->stats($user),
            'ikuProgress' => $user->can('program.verify') ? $this->ikuProgress() : null,
            'myTasks' => $showMyTasks ? $this->myTasks($user) : null,
            'myBookings' => $user->can('ruangan.book') ? $this->myBookings($user) : null,
            'can' => [
                'approveBookings' => $user->can('ruangan.approve'),
            ],
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function stats(User $user): array
    {
        $stats = [];

        if ($user->can('program.view')) {
            $stats['programsOngoing'] = Program::where('status', 'ongoing')->count();
        }

        if ($user->can('teknologi.view')) {
            $stats['technologies'] = Technology::count();
            $stats['ipAssetsGranted'] = IpAsset::where('status', 'granted')->count();
        }

        if ($user->can('mitra.view')) {
            $stats['partners'] = Partner::count();
        }

        if ($user->can('inkubasi.view')) {
            $stats['tenantsActive'] = Tenant::where('status', 'active')->count();
        }

        if ($user->can('ruangan.view')) {
            $stats['bookingsPending'] = RoomBooking::where('status', 'pending')->count();
        }

        if ($user->can('surat.view')) {
            $stats['lettersOpen'] = Letter::where('status', '!=', 'completed')->count();
        }

        return $stats;
    }

    /**
     * Progres indikator IKU: target vs realisasi yang sudah diverifikasi, per kuartal.
     *
     * @return array<int, array<string, mixed>>
     */
    private function ikuProgress(): array
    {
        return ProgramIndicator::query()
            ->where('is_iku', true)
            ->with([
                'program:id,name',
                'realizations' => fn ($query) => $query
                    ->where('status', 'verified')
                    ->orderBy('period'),
            ])
            ->get()
            ->map(fn (ProgramIndicator $indicator) => [
                'id' => $indicator->id,
                'name' => $indicator->name,
                'program' => $indicator->program->name,
                'target' => (float) $indicator->target,
                'realized' => (float) $indicator->realizations->sum('actual_value'),
                'measurementUnit' => $indicator->measurement_unit,
                'quarters' => $indicator->realizations
                    ->map(fn (IndicatorRealization $realization) => [
                        'period' => $realization->period,
                        'value' => (float) $realization->actual_value,
                    ])
                    ->values(),
            ])
            ->values()
            ->all();
    }

    /**
     * "Tugas Saya": disposisi ke unit user yang belum selesai, dan (jika user bisa
     * memverifikasi) realisasi indikator yang menunggu verifikasi.
     *
     * @return array<string, Collection<int, array<string, mixed>>>
     */
    private function myTasks(User $user): array
    {
        $dispositions = Disposition::query()
            ->where(fn ($q) => $q->where('to_unit_id', $user->unit_id)->orWhere('to_user_id', $user->id))
            ->where('status', '!=', 'completed')
            ->with('letter:id,subject')
            ->latest('due_date')
            ->limit(5)
            ->get()
            ->map(fn (Disposition $disposition) => [
                'id' => $disposition->id,
                'letterId' => $disposition->letter_id,
                'subject' => $disposition->letter->subject,
                'instruction' => $disposition->instruction,
                'dueDate' => $disposition->due_date?->toDateString(),
                'status' => $disposition->status,
            ]);

        $pendingRealizations = $user->can('program.verify')
            ? IndicatorRealization::query()
                ->where('status', 'submitted')
                ->with('indicator:id,name,program_id')
                ->limit(5)
                ->get()
                ->map(fn (IndicatorRealization $realization) => [
                    'id' => $realization->id,
                    'programId' => $realization->indicator->program_id,
                    'indicator' => $realization->indicator->name,
                    'period' => $realization->period,
                ])
            : collect();

        $reportableIndicators = $user->can('program.report') && $user->unit_id
            ? ProgramIndicator::query()
                ->whereHas('program', fn ($q) => $q->where('unit_id', $user->unit_id)->where('year', now()->year))
                ->whereDoesntHave('realizations', fn ($q) => $q->where('period', IndicatorRealization::currentPeriod()))
                ->with('program:id,name')
                ->limit(5)
                ->get()
                ->map(fn (ProgramIndicator $indicator) => [
                    'id' => $indicator->id,
                    'programId' => $indicator->program_id,
                    'name' => $indicator->name,
                    'program' => $indicator->program->name,
                    'period' => IndicatorRealization::currentPeriod(),
                ])
            : collect();

        return [
            'dispositions' => $dispositions,
            'pendingRealizations' => $pendingRealizations,
            'reportableIndicators' => $reportableIndicators,
        ];
    }

    /**
     * "Booking Saya": riwayat pemesanan ruangan milik user, dan status tenant jika terhubung.
     *
     * @return array<string, mixed>
     */
    private function myBookings(User $user): array
    {
        $bookings = RoomBooking::query()
            ->where('user_id', $user->id)
            ->with('room:id,name')
            ->latest('booking_date')
            ->limit(5)
            ->get()
            ->map(fn (RoomBooking $booking) => [
                'id' => $booking->id,
                'room' => $booking->room->name,
                'date' => $booking->booking_date->toDateString(),
                'startTime' => $booking->start_time,
                'endTime' => $booking->end_time,
                'status' => $booking->status,
            ]);

        $tenant = Tenant::where('user_id', $user->id)->first();

        return [
            'bookings' => $bookings,
            'tenant' => $tenant ? [
                'startupName' => $tenant->startup_name,
                'stage' => $tenant->stage,
                'status' => $tenant->status,
            ] : null,
        ];
    }
}
