import { Head, Link, router } from '@inertiajs/react';
import { CalendarPlus, X } from 'lucide-react';
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
import { formatStatusLabel } from '@/lib/status-labels';
import booking from '@/routes/ruangan/booking';
import type { Paginated } from '@/types/ui';

type MyBooking = {
    id: number;
    room: string;
    date: string;
    startTime: string;
    endTime: string;
    purpose: string;
    participantCount: number;
    status: string;
    approver: string | null;
    approvalNotes: string | null;
    canCancel: boolean;
};

type Props = {
    bookings: Paginated<MyBooking>;
};

const STATUS_BADGE: Record<
    string,
    'secondary' | 'outline' | 'destructive' | 'default'
> = {
    pending: 'outline',
    approved: 'default',
    rejected: 'destructive',
    cancelled: 'secondary',
};

export default function BookingSaya({ bookings }: Props) {
    return (
        <>
            <Head title="Booking Saya" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Booking Saya"
                        description="Riwayat pengajuan booking ruangan Anda."
                    />
                    <Button asChild>
                        <Link href={booking.create()}>
                            <CalendarPlus />
                            Ajukan Booking
                        </Link>
                    </Button>
                </div>

                <div className="space-y-3">
                    {bookings.data.length === 0 && (
                        <Card className="border-border/60 p-6 text-center text-sm text-muted-foreground shadow-sm">
                            Anda belum memiliki pemesanan ruangan.
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
                                    <Badge
                                        variant={STATUS_BADGE[item.status]}
                                    >
                                        {formatStatusLabel(item.status)}
                                    </Badge>
                                </div>
                                <p className="text-sm text-muted-foreground">
                                    {item.date} · {item.startTime}–
                                    {item.endTime} · {item.participantCount}{' '}
                                    peserta
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

                            {item.canCancel && (
                                <Dialog>
                                    <DialogTrigger asChild>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            className="shrink-0"
                                        >
                                            <X />
                                            Batalkan
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogTitle>
                                            Batalkan booking ini?
                                        </DialogTitle>
                                        <DialogDescription>
                                            Booking {item.room} pada{' '}
                                            {item.date} akan dibatalkan.
                                        </DialogDescription>
                                        <DialogFooter className="gap-2">
                                            <DialogClose asChild>
                                                <Button variant="secondary">
                                                    Tidak
                                                </Button>
                                            </DialogClose>
                                            <Button
                                                variant="destructive"
                                                onClick={() =>
                                                    router.patch(
                                                        booking.cancel.url(
                                                            item.id,
                                                        ),
                                                    )
                                                }
                                            >
                                                Ya, Batalkan
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            )}
                        </Card>
                    ))}
                </div>

                <Pagination paginated={bookings} />
            </div>
        </>
    );
}

BookingSaya.layout = () => ({
    breadcrumbs: [{ title: 'Booking Saya', href: '#' }],
});
