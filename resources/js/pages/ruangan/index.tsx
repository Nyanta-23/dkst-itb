import { Head, Link, router } from '@inertiajs/react';
import { Building2, CalendarPlus, Users } from 'lucide-react';
import Heading from '@/components/heading';
import {
    RoomSchedule,
    type ScheduleBooking,
} from '@/components/ruangan/room-schedule';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import ruangan from '@/routes/ruangan';
import booking from '@/routes/ruangan/booking';

type RoomCard = {
    id: number;
    name: string;
    building: string | null;
    capacity: number;
    facilities: string | null;
    isActive: boolean;
};

type Props = {
    rooms: RoomCard[];
    bookings: ScheduleBooking[];
    date: string;
    can: { book: boolean };
};

export default function RuanganIndex({ rooms, bookings, date, can }: Props) {
    function changeDate(value: string) {
        router.get(
            ruangan.index().url,
            { date: value },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    return (
        <>
            <Head title="Peminjaman Ruangan" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Peminjaman Ruangan"
                    description="Lihat ketersediaan ruangan dan ajukan booking."
                />

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {rooms.map((room) => (
                        <Card key={room.id} className="border-border/60 shadow-sm">
                            <CardHeader>
                                <div className="flex items-start justify-between gap-2">
                                    <CardTitle className="text-base">
                                        {room.name}
                                    </CardTitle>
                                    {!room.isActive && (
                                        <Badge variant="outline">
                                            Nonaktif
                                        </Badge>
                                    )}
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div className="space-y-1 text-sm text-muted-foreground">
                                    {room.building && (
                                        <p className="flex items-center gap-2">
                                            <Building2 className="size-3.5" />
                                            {room.building}
                                        </p>
                                    )}
                                    <p className="flex items-center gap-2">
                                        <Users className="size-3.5" />
                                        Kapasitas {room.capacity} orang
                                    </p>
                                    {room.facilities && (
                                        <p>{room.facilities}</p>
                                    )}
                                </div>

                                {can.book && (
                                    <Button
                                        size="sm"
                                        className="w-full"
                                        disabled={!room.isActive}
                                        asChild={room.isActive}
                                    >
                                        {room.isActive ? (
                                            <Link
                                                href={
                                                    booking.create({
                                                        query: {
                                                            room_id: room.id,
                                                        },
                                                    }).url
                                                }
                                            >
                                                <CalendarPlus />
                                                Ajukan Booking
                                            </Link>
                                        ) : (
                                            <>
                                                <CalendarPlus />
                                                Ajukan Booking
                                            </>
                                        )}
                                    </Button>
                                )}
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <div className="flex flex-col gap-4">
                    <div className="flex flex-wrap items-center justify-between gap-3">
                        <h2 className="text-base font-semibold">
                            Jadwal Harian
                        </h2>
                        <Input
                            type="date"
                            value={date}
                            onChange={(e) => changeDate(e.target.value)}
                            className="w-44"
                        />
                    </div>

                    <div className="flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
                        <span className="flex items-center gap-1.5">
                            <span className="size-3 rounded bg-dkst-navy dark:bg-dkst-cyan-light" />
                            Disetujui
                        </span>
                        <span className="flex items-center gap-1.5">
                            <span className="size-3 rounded border border-dashed border-dkst-navy bg-dkst-navy/20" />
                            Menunggu persetujuan
                        </span>
                    </div>

                    <RoomSchedule
                        rooms={rooms}
                        bookings={bookings}
                        date={date}
                        canBook={can.book}
                    />
                </div>
            </div>
        </>
    );
}

RuanganIndex.layout = () => ({
    breadcrumbs: [{ title: 'Peminjaman Ruangan', href: ruangan.index() }],
});
