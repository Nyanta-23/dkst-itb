import { Head, Link, router, useForm } from '@inertiajs/react';
import { Check, FileText, X } from 'lucide-react';
import { type FormEventHandler } from 'react';
import IndicatorRealizationVerificationController from '@/actions/App/Http/Controllers/Program/IndicatorRealizationVerificationController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Pagination } from '@/components/pagination';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import { formatNumber } from '@/lib/format';
import { show, verifikasi } from '@/routes/program';
import type { Paginated } from '@/types/ui';

type RealizationRow = {
    id: number;
    programId: number;
    program: string;
    unit: string;
    indicator: string;
    period: string;
    actualValue: number;
    notes: string | null;
    evidenceUrl: string | null;
    reporter: string;
    verifier: string | null;
    verificationNotes: string | null;
};

type Props = {
    realizations: Paginated<RealizationRow>;
    tab: 'submitted' | 'verified' | 'rejected';
};

const TABS = [
    { value: 'submitted', label: 'Menunggu' },
    { value: 'verified', label: 'Diverifikasi' },
    { value: 'rejected', label: 'Ditolak' },
] as const;

function RejectDialog({ realizationId }: { realizationId: number }) {
    const { data, setData, patch, processing, errors, reset } = useForm({
        action: 'rejected',
        verification_notes: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(
            IndicatorRealizationVerificationController.update.url(
                realizationId,
            ),
            { preserveScroll: true, onSuccess: () => reset() },
        );
    };

    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="outline" size="sm">
                    <X />
                    Tolak
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Tolak realisasi ini?</DialogTitle>
                <DialogDescription>
                    Berikan alasan penolakan agar pelapor dapat memperbaiki
                    dan mengajukan ulang.
                </DialogDescription>
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid gap-2">
                        <Textarea
                            value={data.verification_notes}
                            onChange={(e) =>
                                setData(
                                    'verification_notes',
                                    e.target.value,
                                )
                            }
                            placeholder="Alasan penolakan…"
                        />
                        <InputError message={errors.verification_notes} />
                    </div>
                    <DialogFooter className="gap-2">
                        <DialogClose asChild>
                            <Button type="button" variant="secondary">
                                Batal
                            </Button>
                        </DialogClose>
                        <Button
                            type="submit"
                            variant="destructive"
                            disabled={processing}
                        >
                            Tolak Realisasi
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}

export default function Verifikasi({ realizations, tab }: Props) {
    function changeTab(value: string) {
        router.get(
            verifikasi().url,
            { tab: value },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    function verify(realizationId: number) {
        router.patch(
            IndicatorRealizationVerificationController.update.url(
                realizationId,
            ),
            { action: 'verified' },
            { preserveScroll: true },
        );
    }

    return (
        <>
            <Head title="Verifikasi Realisasi" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Verifikasi Realisasi"
                    description="Tinjau realisasi indikator yang diajukan dari seluruh unit."
                />

                <div className="flex flex-wrap gap-2">
                    {TABS.map((item) => (
                        <Button
                            key={item.value}
                            size="sm"
                            variant={
                                tab === item.value ? 'default' : 'outline'
                            }
                            onClick={() => changeTab(item.value)}
                        >
                            {item.label}
                        </Button>
                    ))}
                </div>

                <div className="space-y-3">
                    {realizations.data.length === 0 && (
                        <Card className="border-border/60 p-6 text-center text-sm text-muted-foreground shadow-sm">
                            Tidak ada realisasi pada status ini.
                        </Card>
                    )}

                    {realizations.data.map((item) => (
                        <Card
                            key={item.id}
                            className="flex flex-col gap-3 border-border/60 p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div className="space-y-1">
                                <div className="flex items-center gap-2">
                                    <Link
                                        href={show(item.programId)}
                                        className="font-medium hover:underline"
                                    >
                                        {item.program}
                                    </Link>
                                    <Badge variant="secondary">
                                        {item.period}
                                    </Badge>
                                </div>
                                <p className="text-sm text-muted-foreground">
                                    {item.indicator} · {item.unit} · nilai{' '}
                                    {formatNumber(item.actualValue)}
                                </p>
                                <p className="text-sm text-muted-foreground">
                                    Pelapor: {item.reporter}
                                </p>
                                {item.notes && (
                                    <p className="text-sm text-muted-foreground">
                                        Catatan: {item.notes}
                                    </p>
                                )}
                                {item.evidenceUrl && (
                                    <a
                                        href={item.evidenceUrl}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="inline-flex items-center gap-1 text-sm text-dkst-navy underline dark:text-dkst-cyan-light"
                                    >
                                        <FileText className="size-3.5" />
                                        Lihat bukti
                                    </a>
                                )}
                                {item.verifier && (
                                    <p className="text-xs text-muted-foreground">
                                        Ditinjau oleh {item.verifier}
                                    </p>
                                )}
                                {item.verificationNotes && (
                                    <p className="text-sm text-destructive">
                                        Alasan: {item.verificationNotes}
                                    </p>
                                )}
                            </div>

                            {tab === 'submitted' && (
                                <div className="flex shrink-0 gap-2">
                                    <Button
                                        size="sm"
                                        onClick={() => verify(item.id)}
                                    >
                                        <Check />
                                        Verifikasi
                                    </Button>
                                    <RejectDialog realizationId={item.id} />
                                </div>
                            )}
                        </Card>
                    ))}
                </div>

                <Pagination paginated={realizations} />
            </div>
        </>
    );
}

Verifikasi.layout = () => ({
    breadcrumbs: [{ title: 'Verifikasi Realisasi', href: '#' }],
});
