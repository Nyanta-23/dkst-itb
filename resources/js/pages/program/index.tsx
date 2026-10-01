import { Head, Link, router } from '@inertiajs/react';
import { Download, Plus } from 'lucide-react';
import Heading from '@/components/heading';
import { Pagination } from '@/components/pagination';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Progress } from '@/components/ui/progress';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { formatRupiah } from '@/lib/format';
import { formatStatusLabel } from '@/lib/status-labels';
import { create, exportIku, index, show } from '@/routes/program';
import type { Paginated } from '@/types/ui';

type ProgramRow = {
    id: number;
    code: string;
    name: string;
    unit: string;
    pic: string | null;
    budget: number;
    status: string;
    progress: number;
};

type Filters = {
    year: number;
    unitId: number | null;
    status: string;
    search: string;
};

type Props = {
    programs: Paginated<ProgramRow>;
    filters: Filters;
    units: { id: number; name: string }[];
    years: number[];
    can: { manage: boolean; exportIku: boolean };
};

export default function ProgramIndex({
    programs,
    filters,
    units,
    years,
    can,
}: Props) {
    function navigate(overrides: Partial<Filters>) {
        router.get(
            index().url,
            { ...filters, ...overrides },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    return (
        <>
            <Head title="Program & Monev" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Program & Monev"
                        description="Kelola program kerja, indikator kinerja, dan realisasinya."
                    />
                    <div className="flex flex-wrap gap-2">
                        {can.exportIku && (
                            <Button variant="outline" asChild>
                                <a
                                    href={
                                        exportIku({
                                            query: { year: filters.year },
                                        }).url
                                    }
                                >
                                    <Download />
                                    Export IKU
                                </a>
                            </Button>
                        )}
                        {can.manage && (
                            <Button asChild>
                                <Link href={create()}>
                                    <Plus />
                                    Tambah Program
                                </Link>
                            </Button>
                        )}
                    </div>
                </div>

                <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <Input
                        defaultValue={filters.search}
                        onBlur={(e) => navigate({ search: e.target.value })}
                        onKeyDown={(e) => {
                            if (e.key === 'Enter') {
                                navigate({
                                    search: (e.target as HTMLInputElement)
                                        .value,
                                });
                            }
                        }}
                        placeholder="Cari nama atau kode program…"
                        className="sm:max-w-xs"
                    />

                    <Select
                        value={String(filters.year)}
                        onValueChange={(value) =>
                            navigate({ year: Number(value) })
                        }
                    >
                        <SelectTrigger className="sm:w-32">
                            <SelectValue placeholder="Tahun" />
                        </SelectTrigger>
                        <SelectContent>
                            {years.map((year) => (
                                <SelectItem key={year} value={String(year)}>
                                    {year}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <Select
                        value={filters.unitId ? String(filters.unitId) : 'all'}
                        onValueChange={(value) =>
                            navigate({
                                unitId: value === 'all' ? null : Number(value),
                            })
                        }
                    >
                        <SelectTrigger className="sm:w-48">
                            <SelectValue placeholder="Unit" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Unit</SelectItem>
                            {units.map((unit) => (
                                <SelectItem
                                    key={unit.id}
                                    value={String(unit.id)}
                                >
                                    {unit.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <Select
                        value={filters.status || 'all'}
                        onValueChange={(value) =>
                            navigate({ status: value === 'all' ? '' : value })
                        }
                    >
                        <SelectTrigger className="sm:w-40">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Status</SelectItem>
                            <SelectItem value="draft">Draft</SelectItem>
                            <SelectItem value="ongoing">Berjalan</SelectItem>
                            <SelectItem value="completed">Selesai</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <Card className="overflow-hidden border-border/60 p-0 shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b bg-muted/40 text-left text-xs font-medium text-muted-foreground uppercase">
                                <tr>
                                    <th className="px-4 py-3">Kode</th>
                                    <th className="px-4 py-3">Nama</th>
                                    <th className="px-4 py-3">Unit</th>
                                    <th className="px-4 py-3">PIC</th>
                                    <th className="px-4 py-3">Anggaran</th>
                                    <th className="px-4 py-3">Status</th>
                                    <th className="px-4 py-3">Progres</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {programs.data.map((program) => (
                                    <tr
                                        key={program.id}
                                        onClick={() =>
                                            router.visit(show(program.id))
                                        }
                                        className="cursor-pointer hover:bg-accent"
                                    >
                                        <td className="px-4 py-3 font-medium whitespace-nowrap">
                                            {program.code}
                                        </td>
                                        <td className="max-w-xs truncate px-4 py-3">
                                            {program.name}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                            {program.unit}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                            {program.pic ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                            {formatRupiah(program.budget)}
                                        </td>
                                        <td className="px-4 py-3">
                                            <Badge variant="secondary">
                                                {formatStatusLabel(
                                                    program.status,
                                                )}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-2">
                                                <Progress
                                                    value={program.progress}
                                                    className="w-24"
                                                />
                                                <span className="text-xs text-muted-foreground">
                                                    {program.progress}%
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                ))}

                                {programs.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={7}
                                            className="px-4 py-10 text-center text-muted-foreground"
                                        >
                                            Tidak ada program yang ditemukan.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </Card>

                <Pagination paginated={programs} />
            </div>
        </>
    );
}

ProgramIndex.layout = () => ({
    breadcrumbs: [{ title: 'Program & Monev', href: index() }],
});
