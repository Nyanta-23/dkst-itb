<?php

namespace App\Exports;

use App\Models\ProgramIndicator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Export seluruh indikator IKU pada tahun tertentu: target, realisasi
 * terverifikasi per kuartal, total, dan persentase capaian.
 */
class IkuExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly int $year) {}

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function collection(): Collection
    {
        return ProgramIndicator::query()
            ->where('is_iku', true)
            ->whereHas('program', fn ($query) => $query->where('year', $this->year))
            ->with([
                'program.unit:id,name',
                'realizations' => fn ($query) => $query->where('status', 'verified'),
            ])
            ->get()
            ->map(function (ProgramIndicator $indicator) {
                $quarterly = collect(['Q1', 'Q2', 'Q3', 'Q4'])
                    ->mapWithKeys(function (string $quarter) use ($indicator) {
                        $period = "{$this->year}-{$quarter}";
                        $value = $indicator->realizations->firstWhere('period', $period)?->actual_value;

                        return [$quarter => (float) ($value ?? 0)];
                    });

                $target = (float) $indicator->target;
                $total = $quarterly->sum();
                $percentage = $target > 0 ? round(($total / $target) * 100, 2) : 0.0;

                return [
                    'iku_code' => $indicator->iku_code,
                    'name' => $indicator->name,
                    'program' => $indicator->program->name,
                    'unit' => $indicator->program->unit->name,
                    'target' => $target,
                    'q1' => $quarterly['Q1'],
                    'q2' => $quarterly['Q2'],
                    'q3' => $quarterly['Q3'],
                    'q4' => $quarterly['Q4'],
                    'total' => $total,
                    'percentage' => $percentage,
                ];
            })
            ->values();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'Kode IKU',
            'Indikator',
            'Program',
            'Unit',
            'Target',
            'Q1',
            'Q2',
            'Q3',
            'Q4',
            'Total Realisasi',
            'Persentase (%)',
        ];
    }
}
