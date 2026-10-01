import { FilePlus2, FileText } from 'lucide-react';
import { RealizationFormDialog } from '@/components/program/realization-form-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { formatNumber } from '@/lib/format';
import { formatStatusLabel } from '@/lib/status-labels';

export type IndicatorDetail = {
    id: number;
    name: string;
    isIku: boolean;
    ikuCode: string | null;
    target: number;
    measurementUnit: string;
    achieved: number;
    percentage: number;
    canReport: boolean;
    periods: {
        period: string;
        quarter: string;
        realization: {
            id: number;
            actualValue: number;
            notes: string | null;
            evidenceUrl: string | null;
            status: string;
            reporter: string;
            verifier: string | null;
            verificationNotes: string | null;
        } | null;
    }[];
};

type Props = {
    programId: number;
    indicator: IndicatorDetail;
};

export function IndicatorCard({ programId, indicator }: Props) {
    return (
        <Card className="border-border/60 shadow-sm">
            <CardHeader>
                <div className="flex flex-wrap items-center justify-between gap-2">
                    <CardTitle className="text-base">
                        {indicator.name}
                    </CardTitle>
                    {indicator.isIku && (
                        <Badge variant="outline">
                            IKU {indicator.ikuCode}
                        </Badge>
                    )}
                </div>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className="space-y-1.5">
                    <div className="flex items-baseline justify-between text-sm">
                        <span className="font-semibold">
                            {formatNumber(indicator.achieved)}
                            <span className="text-muted-foreground">
                                {' '}
                                / {formatNumber(indicator.target)}{' '}
                                {indicator.measurementUnit}
                            </span>
                        </span>
                        <span className="text-xs font-medium text-dkst-navy dark:text-dkst-cyan-light">
                            {indicator.percentage}%
                        </span>
                    </div>
                    <Progress value={indicator.percentage} />
                </div>

                <div className="overflow-hidden rounded-lg border border-border/60">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/40 text-left text-xs font-medium text-muted-foreground uppercase">
                            <tr>
                                <th className="px-3 py-2">Periode</th>
                                <th className="px-3 py-2">Nilai</th>
                                <th className="px-3 py-2">Status</th>
                                <th className="px-3 py-2">Bukti</th>
                                <th className="px-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y">
                            {indicator.periods.map((period) => {
                                const realization = period.realization;
                                const canSubmit =
                                    indicator.canReport &&
                                    (!realization ||
                                        realization.status === 'rejected');

                                return (
                                    <tr key={period.period}>
                                        <td className="px-3 py-2 font-medium">
                                            {period.quarter}
                                        </td>
                                        <td className="px-3 py-2 text-muted-foreground">
                                            {realization
                                                ? `${formatNumber(realization.actualValue)} ${indicator.measurementUnit}`
                                                : '—'}
                                        </td>
                                        <td className="px-3 py-2">
                                            <Badge variant="secondary">
                                                {realization
                                                    ? formatStatusLabel(
                                                          realization.status,
                                                      )
                                                    : 'Belum diisi'}
                                            </Badge>
                                            {realization?.status ===
                                                'rejected' &&
                                                realization.verificationNotes && (
                                                    <p className="mt-1 text-xs text-destructive">
                                                        {
                                                            realization.verificationNotes
                                                        }
                                                    </p>
                                                )}
                                        </td>
                                        <td className="px-3 py-2">
                                            {realization?.evidenceUrl ? (
                                                <a
                                                    href={
                                                        realization.evidenceUrl
                                                    }
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="inline-flex items-center gap-1 text-dkst-navy underline dark:text-dkst-cyan-light"
                                                >
                                                    <FileText className="size-3.5" />
                                                    Lihat
                                                </a>
                                            ) : (
                                                <span className="text-muted-foreground">
                                                    —
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-3 py-2">
                                            {canSubmit && (
                                                <RealizationFormDialog
                                                    programId={programId}
                                                    indicatorId={indicator.id}
                                                    period={period.period}
                                                    measurementUnit={
                                                        indicator.measurementUnit
                                                    }
                                                    existing={
                                                        realization
                                                            ? {
                                                                  actualValue:
                                                                      realization.actualValue,
                                                                  notes: realization.notes,
                                                                  verificationNotes:
                                                                      realization.verificationNotes,
                                                              }
                                                            : undefined
                                                    }
                                                    trigger={
                                                        <Button
                                                            size="sm"
                                                            variant="outline"
                                                        >
                                                            <FilePlus2 />
                                                            {realization
                                                                ? 'Ajukan Ulang'
                                                                : 'Input Realisasi'}
                                                        </Button>
                                                    }
                                                />
                                            )}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    );
}
