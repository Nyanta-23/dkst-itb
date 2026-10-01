import { Link } from '@inertiajs/react';
import { CalendarCheck, CalendarPlus, Rocket } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatStatusLabel } from '@/lib/status-labels';
import ruangan from '@/routes/ruangan';

export type MyBookings = {
    bookings: {
        id: number;
        room: string;
        date: string;
        startTime: string;
        endTime: string;
        status: string;
    }[];
    tenant: {
        startupName: string;
        stage: string;
        status: string;
    } | null;
};

export function MyBookingsCard({ data }: { data: MyBookings }) {
    return (
        <Card className="border-border/60 shadow-sm">
            <CardHeader className="flex-row items-center justify-between">
                <CardTitle>Booking Saya</CardTitle>
                <Button size="sm" asChild>
                    <Link href={ruangan.booking.create()}>
                        <CalendarPlus />
                        Ajukan Booking
                    </Link>
                </Button>
            </CardHeader>
            <CardContent className="space-y-4">
                {data.tenant && (
                    <div className="flex items-center gap-3 rounded-lg border border-border/60 p-3 text-sm">
                        <Rocket className="size-4 shrink-0 text-dkst-navy dark:text-dkst-cyan-light" />
                        <div className="flex-1">
                            <p className="font-medium">
                                {data.tenant.startupName}
                            </p>
                            <p className="text-xs text-muted-foreground">
                                Tahap:{' '}
                                {formatStatusLabel(data.tenant.stage)}
                            </p>
                        </div>
                        <Badge variant="secondary">
                            {formatStatusLabel(data.tenant.status)}
                        </Badge>
                    </div>
                )}

                {data.bookings.length === 0 ? (
                    <p className="flex items-center gap-2 text-sm text-muted-foreground">
                        <CalendarCheck className="size-4" />
                        Anda belum memiliki pemesanan ruangan.
                    </p>
                ) : (
                    data.bookings.map((booking) => (
                        <div
                            key={booking.id}
                            className="flex items-center justify-between rounded-lg border border-border/60 p-3 text-sm"
                        >
                            <div>
                                <p className="font-medium">{booking.room}</p>
                                <p className="text-xs text-muted-foreground">
                                    {booking.date} · {booking.startTime}–
                                    {booking.endTime}
                                </p>
                            </div>
                            <Badge variant="secondary">
                                {formatStatusLabel(booking.status)}
                            </Badge>
                        </div>
                    ))
                )}
            </CardContent>
        </Card>
    );
}
