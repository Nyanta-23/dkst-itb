import { Head, Link } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import Heading from '@/components/heading';
import {
    IndicatorCard,
    type IndicatorDetail,
} from '@/components/program/indicator-card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { formatRupiah } from '@/lib/format';
import { formatStatusLabel } from '@/lib/status-labels';
import { edit, index } from '@/routes/program';

type ProgramDetail = {
    id: number;
    code: string;
    name: string;
    description: string | null;
    unit: string;
    pic: string | null;
    year: number;
    budget: number;
    startDate: string;
    endDate: string | null;
    status: string;
};

type Props = {
    program: ProgramDetail;
    indicators: IndicatorDetail[];
    can: { manage: boolean };
};

export default function ProgramShow({ program, indicators, can }: Props) {
    return (
        <>
            <Head title={program.name} />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div className="space-y-2">
                        <Heading
                            title={program.name}
                            description={`${program.code} · ${program.unit}`}
                        />
                        <Badge variant="secondary">
                            {formatStatusLabel(program.status)}
                        </Badge>
                    </div>

                    {can.manage && (
                        <Button variant="outline" asChild>
                            <Link href={edit(program.id)}>
                                <Pencil />
                                Edit
                            </Link>
                        </Button>
                    )}
                </div>

                <Card className="border-border/60 shadow-sm">
                    <CardContent className="grid gap-4 pt-6 text-sm sm:grid-cols-2 lg:grid-cols-3">
                        {program.description && (
                            <div className="sm:col-span-2 lg:col-span-3">
                                <p className="text-xs text-muted-foreground uppercase">
                                    Deskripsi
                                </p>
                                <p className="font-medium">
                                    {program.description}
                                </p>
                            </div>
                        )}
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                PIC
                            </p>
                            <p className="font-medium">
                                {program.pic ?? '—'}
                            </p>
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                Tahun
                            </p>
                            <p className="font-medium">{program.year}</p>
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                Anggaran
                            </p>
                            <p className="font-medium">
                                {formatRupiah(program.budget)}
                            </p>
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                Tanggal Mulai
                            </p>
                            <p className="font-medium">
                                {program.startDate}
                            </p>
                        </div>
                        {program.endDate && (
                            <div>
                                <p className="text-xs text-muted-foreground uppercase">
                                    Tanggal Selesai
                                </p>
                                <p className="font-medium">
                                    {program.endDate}
                                </p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                <div className="space-y-4">
                    <h2 className="text-base font-semibold">Indikator</h2>
                    {indicators.map((indicator) => (
                        <IndicatorCard
                            key={indicator.id}
                            programId={program.id}
                            indicator={indicator}
                        />
                    ))}
                </div>
            </div>
        </>
    );
}

ProgramShow.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Program & Monev', href: index() },
        { title: props.program.name, href: '#' },
    ],
});
