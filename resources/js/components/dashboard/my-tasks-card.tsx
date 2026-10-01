import { Link } from '@inertiajs/react';
import { CheckCircle2, ClipboardList, FileText } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatStatusLabel } from '@/lib/status-labels';
import { show as showProgram, verifikasi } from '@/routes/program';
import { show } from '@/routes/surat';

export type MyTasks = {
    dispositions: {
        id: number;
        letterId: number;
        subject: string;
        instruction: string;
        dueDate: string | null;
        status: string;
    }[];
    pendingRealizations: {
        id: number;
        programId: number;
        indicator: string;
        period: string;
    }[];
    reportableIndicators: {
        id: number;
        programId: number;
        name: string;
        program: string;
        period: string;
    }[];
};

export function MyTasksCard({ tasks }: { tasks: MyTasks }) {
    const isEmpty =
        tasks.dispositions.length === 0 &&
        tasks.pendingRealizations.length === 0 &&
        tasks.reportableIndicators.length === 0;

    return (
        <Card className="border-border/60 shadow-sm">
            <CardHeader>
                <CardTitle>Tugas Saya</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
                {isEmpty && (
                    <p className="flex items-center gap-2 text-sm text-muted-foreground">
                        <CheckCircle2 className="size-4" />
                        Tidak ada tugas yang menunggu saat ini.
                    </p>
                )}

                {tasks.dispositions.length > 0 && (
                    <div className="space-y-2">
                        <p className="text-xs font-medium text-muted-foreground uppercase">
                            Disposisi Belum Selesai
                        </p>
                        {tasks.dispositions.map((disposition) => (
                            <Link
                                key={disposition.id}
                                href={show(disposition.letterId)}
                                className="flex items-start gap-3 rounded-lg border border-border/60 p-3 text-sm transition-colors hover:bg-accent"
                            >
                                <FileText className="mt-0.5 size-4 shrink-0 text-dkst-navy dark:text-dkst-cyan-light" />
                                <div className="flex-1 space-y-0.5">
                                    <p className="font-medium">
                                        {disposition.subject}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {disposition.instruction}
                                    </p>
                                    {disposition.dueDate && (
                                        <p className="text-xs text-muted-foreground">
                                            Batas waktu: {disposition.dueDate}
                                        </p>
                                    )}
                                </div>
                                <Badge variant="secondary">
                                    {formatStatusLabel(disposition.status)}
                                </Badge>
                            </Link>
                        ))}
                    </div>
                )}

                {tasks.pendingRealizations.length > 0 && (
                    <div className="space-y-2">
                        <p className="text-xs font-medium text-muted-foreground uppercase">
                            Realisasi Menunggu Verifikasi
                        </p>
                        {tasks.pendingRealizations.map((realization) => (
                            <Link
                                key={realization.id}
                                href={verifikasi()}
                                className="flex items-center justify-between rounded-lg border border-border/60 p-3 text-sm transition-colors hover:bg-accent"
                            >
                                <span className="font-medium">
                                    {realization.indicator}
                                </span>
                                <Badge variant="secondary">
                                    {realization.period}
                                </Badge>
                            </Link>
                        ))}
                    </div>
                )}

                {tasks.reportableIndicators.length > 0 && (
                    <div className="space-y-2">
                        <p className="text-xs font-medium text-muted-foreground uppercase">
                            Indikator Belum Ada Realisasi
                        </p>
                        {tasks.reportableIndicators.map((indicator) => (
                            <Link
                                key={indicator.id}
                                href={showProgram(indicator.programId)}
                                className="flex items-start gap-3 rounded-lg border border-border/60 p-3 text-sm transition-colors hover:bg-accent"
                            >
                                <ClipboardList className="mt-0.5 size-4 shrink-0 text-dkst-navy dark:text-dkst-cyan-light" />
                                <div className="flex-1 space-y-0.5">
                                    <p className="font-medium">
                                        {indicator.name}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {indicator.program}
                                    </p>
                                </div>
                                <Badge variant="secondary">
                                    {indicator.period}
                                </Badge>
                            </Link>
                        ))}
                    </div>
                )}
            </CardContent>
        </Card>
    );
}
