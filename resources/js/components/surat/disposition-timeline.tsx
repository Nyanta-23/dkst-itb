import { router } from '@inertiajs/react';
import { CheckCircle2, Clock, UserRound } from 'lucide-react';
import DispositionController from '@/actions/App/Http/Controllers/Surat/DispositionController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatStatusLabel } from '@/lib/status-labels';

export type DispositionItem = {
    id: number;
    fromUser: string;
    toUnit: string;
    toUser: string | null;
    instruction: string;
    dueDate: string | null;
    status: string;
    readAt: string | null;
    createdAt: string;
    canUpdateStatus: boolean;
};

type Props = {
    letterId: number;
    dispositions: DispositionItem[];
};

export function DispositionTimeline({ letterId, dispositions }: Props) {
    function updateStatus(dispositionId: number, status: string) {
        router.patch(
            DispositionController.update.url({
                letter: letterId,
                disposition: dispositionId,
            }),
            { status },
            { preserveScroll: true },
        );
    }

    return (
        <Card className="border-border/60 shadow-sm">
            <CardHeader>
                <CardTitle>Riwayat Disposisi</CardTitle>
            </CardHeader>
            <CardContent>
                {dispositions.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                        Surat ini belum didisposisikan.
                    </p>
                ) : (
                    <ol className="space-y-4">
                        {dispositions.map((disposition) => (
                            <li
                                key={disposition.id}
                                className="rounded-lg border border-border/60 p-4"
                            >
                                <div className="flex flex-wrap items-center justify-between gap-2">
                                    <div className="flex items-center gap-2 text-sm font-medium">
                                        <UserRound className="size-4 text-dkst-navy dark:text-dkst-cyan-light" />
                                        {disposition.fromUser}
                                        <span className="text-muted-foreground">
                                            →
                                        </span>
                                        {disposition.toUnit}
                                        {disposition.toUser &&
                                            ` (${disposition.toUser})`}
                                    </div>
                                    <Badge variant="secondary">
                                        {formatStatusLabel(disposition.status)}
                                    </Badge>
                                </div>

                                <p className="mt-2 text-sm text-muted-foreground">
                                    {disposition.instruction}
                                </p>

                                <div className="mt-3 flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                                    {disposition.dueDate && (
                                        <span className="flex items-center gap-1">
                                            <Clock className="size-3.5" />
                                            Batas waktu: {disposition.dueDate}
                                        </span>
                                    )}
                                    {disposition.readAt && (
                                        <span className="flex items-center gap-1">
                                            <CheckCircle2 className="size-3.5" />
                                            Dibaca:{' '}
                                            {new Date(
                                                disposition.readAt,
                                            ).toLocaleString('id-ID')}
                                        </span>
                                    )}
                                </div>

                                {disposition.canUpdateStatus && (
                                    <div className="mt-3 flex gap-2">
                                        {disposition.status !==
                                            'in_progress' && (
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                onClick={() =>
                                                    updateStatus(
                                                        disposition.id,
                                                        'in_progress',
                                                    )
                                                }
                                            >
                                                Tandai Diproses
                                            </Button>
                                        )}
                                        <Button
                                            size="sm"
                                            onClick={() =>
                                                updateStatus(
                                                    disposition.id,
                                                    'completed',
                                                )
                                            }
                                        >
                                            Tandai Selesai
                                        </Button>
                                    </div>
                                )}
                            </li>
                        ))}
                    </ol>
                )}
            </CardContent>
        </Card>
    );
}
