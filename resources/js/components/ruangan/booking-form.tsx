import { useForm } from '@inertiajs/react';
import { type FormEventHandler } from 'react';
import RoomBookingController from '@/actions/App/Http/Controllers/Ruangan/RoomBookingController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type Room = { id: number; name: string; capacity: number };

type Props = {
    rooms: Room[];
    prefill: {
        roomId: number | null;
        date: string | null;
        startTime: string | null;
    };
};

type FormData = {
    room_id: string;
    booking_date: string;
    start_time: string;
    end_time: string;
    purpose: string;
    participant_count: string;
};

function addOneHour(time: string): string {
    const [hour, minute] = time.split(':').map(Number);
    const nextHour = Math.min(hour + 1, 21);

    return `${String(nextHour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
}

export function BookingForm({ rooms, prefill }: Props) {
    const { data, setData, post, processing, errors } = useForm<FormData>({
        room_id: prefill.roomId ? String(prefill.roomId) : '',
        booking_date: prefill.date ?? new Date().toISOString().slice(0, 10),
        start_time: prefill.startTime ?? '',
        end_time: prefill.startTime ? addOneHour(prefill.startTime) : '',
        purpose: '',
        participant_count: '',
    });

    const selectedRoom = rooms.find((room) => String(room.id) === data.room_id);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(RoomBookingController.store.url());
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid gap-4 sm:grid-cols-2">
                <div className="grid gap-2 sm:col-span-2">
                    <Label htmlFor="room_id">Ruangan</Label>
                    <Select
                        value={data.room_id}
                        onValueChange={(value) => setData('room_id', value)}
                    >
                        <SelectTrigger id="room_id" className="w-full">
                            <SelectValue placeholder="Pilih ruangan" />
                        </SelectTrigger>
                        <SelectContent>
                            {rooms.map((room) => (
                                <SelectItem
                                    key={room.id}
                                    value={String(room.id)}
                                >
                                    {room.name} (kapasitas {room.capacity})
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.room_id} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="booking_date">Tanggal</Label>
                    <Input
                        id="booking_date"
                        type="date"
                        value={data.booking_date}
                        onChange={(e) =>
                            setData('booking_date', e.target.value)
                        }
                    />
                    <InputError message={errors.booking_date} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="participant_count">Jumlah Peserta</Label>
                    <Input
                        id="participant_count"
                        type="number"
                        min={1}
                        max={selectedRoom?.capacity}
                        value={data.participant_count}
                        onChange={(e) =>
                            setData('participant_count', e.target.value)
                        }
                        placeholder={
                            selectedRoom
                                ? `Maks. ${selectedRoom.capacity} orang`
                                : undefined
                        }
                    />
                    <InputError message={errors.participant_count} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="start_time">Jam Mulai</Label>
                    <Input
                        id="start_time"
                        type="time"
                        min="07:00"
                        max="21:00"
                        value={data.start_time}
                        onChange={(e) =>
                            setData('start_time', e.target.value)
                        }
                    />
                    <InputError message={errors.start_time} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="end_time">Jam Selesai</Label>
                    <Input
                        id="end_time"
                        type="time"
                        min="07:00"
                        max="21:00"
                        value={data.end_time}
                        onChange={(e) => setData('end_time', e.target.value)}
                    />
                    <InputError message={errors.end_time} />
                </div>

                <div className="grid gap-2 sm:col-span-2">
                    <Label htmlFor="purpose">Keperluan</Label>
                    <Input
                        id="purpose"
                        value={data.purpose}
                        onChange={(e) => setData('purpose', e.target.value)}
                        placeholder="Misal: Rapat koordinasi tim"
                    />
                    <InputError message={errors.purpose} />
                </div>
            </div>

            <Button type="submit" disabled={processing}>
                Ajukan Booking
            </Button>
        </form>
    );
}
