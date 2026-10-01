import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { BookingForm } from '@/components/ruangan/booking-form';
import { Card, CardContent } from '@/components/ui/card';
import ruangan from '@/routes/ruangan';
import booking from '@/routes/ruangan/booking';

type Props = {
    rooms: { id: number; name: string; capacity: number }[];
    prefill: {
        roomId: number | null;
        date: string | null;
        startTime: string | null;
    };
};

export default function BookingCreate({ rooms, prefill }: Props) {
    return (
        <>
            <Head title="Ajukan Booking Ruangan" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Ajukan Booking Ruangan"
                    description="Isi detail pemesanan ruangan yang ingin diajukan."
                />

                <Card className="border-border/60 shadow-sm">
                    <CardContent className="pt-6">
                        <BookingForm rooms={rooms} prefill={prefill} />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

BookingCreate.layout = () => ({
    breadcrumbs: [
        { title: 'Peminjaman Ruangan', href: ruangan.index() },
        { title: 'Ajukan Booking', href: booking.create() },
    ],
});
