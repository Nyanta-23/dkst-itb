import { Head, router, useForm } from '@inertiajs/react';
import { Check, X } from 'lucide-react';
import { type FormEventHandler } from 'react';
import RoomBookingApprovalController from '@/actions/App/Http/Controllers/Ruangan/RoomBookingApprovalController';
import Heading from '@/components/heading';
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
import InputError from '@/components/input-error';
import { Textarea } from '@/components/ui/textarea';
import { formatStatusLabel } from '@/lib/status-labels';
import approvals from '@/routes/ruangan/approvals';
import type { Paginated } from '@/types/ui';

type BookingRow = {
    id: number;
    room: string;
    user: string;
    date: string;
    startTime: string;
    endTime: string;
    purpose: string;
    participantCount: number;
    status: string;
    approver: string | null;
    approvalNotes: string | null;
};

type Props = {
    bookings: Paginated<BookingRow>;
    tab: 'pending' | 'approved' | 'rejected';
};

const TABS = [
    { value: 'pending', label: 'Menunggu' },
    { value: 'approved', label: 'Disetujui' },
    { value: 'rejected', label: 'Ditolak' },
] as const;

function RejectDialog({ bookingId }: { bookingId: number }) {
    const { data, setData, patch, processing, errors, reset } = useForm({
        action: 'rejected',
        approval_notes: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(RoomBookingApprovalController.update.url(bookingId), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
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
                <DialogTitle>Tolak pengajuan booking ini?</DialogTitle>
                <DialogDescription>
                    Berikan alasan penolakan agar pemohon mengetahui tindak
                    lanjutnya.
                </DialogDescription>
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid gap-2">
                        <Textarea
                            value={data.approval_notes}
                            onChange={(e) =>
                                setData('approval_notes', e.target.value)
                            }
                            placeholder="Alasan penolakan…"
                        />
                        <InputError message={errors.approval_notes} />
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
                            Tolak Booking
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}

export default function Persetujuan({ bookings, tab }: Props) {
    function changeTab(value: string) {
        router.get(
            approvals.index().url,
            { tab: value },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    function approve(bookingId: number) {
        router.patch(
            RoomBookingApprovalController.update.url(bookingId),
            { action: 'approved' },
            { preserveScroll: true },
        );
    }

    return (
        <>
            <Head title="Persetujuan Ruangan" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Persetujuan Ruangan"
                    description="Tinjau pengajuan booking ruangan dari seluruh user."
                />

                <div className="flex flex-wrap gap-2">
                    {TABS.map((item) => (
                        <Button
                            key={item.value}
                            size="sm"
                            variant={tab === item.value ? 'default' : 'outline'}
                            onClick={() => changeTab(item.value)}
                        >
                            {item.label}
                        </Button>
                    ))}
                </div>

                <div className="space-y-3">
                    {bookings.data.length === 0 && (
                        <Card className="border-border/60 p-6 text-center text-sm text-muted-foreground shadow-sm">
                            Tidak ada booking pada status ini.
                        </Card>
                    )}

                    {bookings.data.map((item) => (
                        <Card
                            key={item.id}
                            className="flex flex-col gap-3 border-border/60 p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div className="space-y-1">
                                <div className="flex items-center gap-2">
                                    <p className="font-medium">
                                        {item.room}
                                    </p>
                                    <Badge variant="secondary">
                                        {formatStatusLabel(item.status)}
                                    </Badge>
                                </div>
                                <p className="text-sm text-muted-foreground">
                                    {item.user} · {item.date} ·{' '}
                                    {item.startTime}–{item.endTime} ·{' '}
                                    {item.participantCount} peserta
                                </p>
                                <p className="text-sm text-muted-foreground">
                                    {item.purpose}
                                </p>
                                {item.status === 'rejected' &&
                                    item.approvalNotes && (
                                        <p className="text-sm text-destructive">
                                            Alasan: {item.approvalNotes}
                                        </p>
                                    )}
                                {item.approver && (
                                    <p className="text-xs text-muted-foreground">
                                        Ditinjau oleh {item.approver}
                                    </p>
                                )}
                            </div>

                            {item.status === 'pending' && (
                                <div className="flex shrink-0 gap-2">
                                    <Button
                                        size="sm"
                                        onClick={() => approve(item.id)}
                                    >
                                        <Check />
                                        Setujui
                                    </Button>
                                    <RejectDialog bookingId={item.id} />
                                </div>
                            )}
                        </Card>
                    ))}
                </div>

                <Pagination paginated={bookings} />
            </div>
        </>
    );
}

Persetujuan.layout = () => ({
    breadcrumbs: [{ title: 'Persetujuan Ruangan', href: '#' }],
});
