import { router } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import booking from '@/routes/ruangan/booking';

export type ScheduleBooking = {
    id: number;
    roomId: number;
    startTime: string;
    endTime: string;
    status: string;
    purpose: string;
    user: string;
};

type ScheduleRoom = {
    id: number;
    name: string;
    isActive: boolean;
};

type Props = {
    rooms: ScheduleRoom[];
    bookings: ScheduleBooking[];
    date: string;
    canBook: boolean;
};

const START_HOUR = 7;
const END_HOUR = 21;
const HOURS = Array.from(
    { length: END_HOUR - START_HOUR },
    (_, i) => START_HOUR + i,
);
const DAY_START_MINUTES = START_HOUR * 60;
const DAY_SPAN_MINUTES = (END_HOUR - START_HOUR) * 60;

function timeToMinutes(time: string): number {
    const [hour, minute] = time.split(':').map(Number);

    return hour * 60 + minute;
}

export function RoomSchedule({ rooms, bookings, date, canBook }: Props) {
    function handleSlotClick(room: ScheduleRoom, hour: number) {
        if (!canBook || !room.isActive) {
            return;
        }

        router.visit(
            booking.create({
                query: {
                    room_id: room.id,
                    date,
                    start_time: `${String(hour).padStart(2, '0')}:00`,
                },
            }).url,
        );
    }

    return (
        <div className="overflow-x-auto rounded-xl border border-border/60">
            <div className="min-w-[800px]">
                <div className="flex border-b bg-muted/40">
                    <div className="w-40 shrink-0 p-2 text-xs font-medium text-muted-foreground uppercase">
                        Ruangan
                    </div>
                    <div
                        className="grid flex-1"
                        style={{
                            gridTemplateColumns: `repeat(${HOURS.length}, 1fr)`,
                        }}
                    >
                        {HOURS.map((hour) => (
                            <div
                                key={hour}
                                className="border-l border-border/40 p-2 text-center text-xs text-muted-foreground"
                            >
                                {String(hour).padStart(2, '0')}:00
                            </div>
                        ))}
                    </div>
                </div>

                {rooms.map((room) => {
                    const roomBookings = bookings.filter(
                        (item) => item.roomId === room.id,
                    );

                    return (
                        <div
                            key={room.id}
                            className="flex border-b border-border/40 last:border-0"
                        >
                            <div className="flex w-40 shrink-0 items-center p-2 text-sm font-medium">
                                {room.name}
                                {!room.isActive && (
                                    <span className="ml-1 text-xs text-muted-foreground">
                                        (nonaktif)
                                    </span>
                                )}
                            </div>
                            <div className="relative h-12 flex-1">
                                <div
                                    className="absolute inset-0 grid"
                                    style={{
                                        gridTemplateColumns: `repeat(${HOURS.length}, 1fr)`,
                                    }}
                                >
                                    {HOURS.map((hour) => (
                                        <button
                                            key={hour}
                                            type="button"
                                            disabled={
                                                !canBook || !room.isActive
                                            }
                                            onClick={() =>
                                                handleSlotClick(room, hour)
                                            }
                                            aria-label={`Booking ${room.name} jam ${hour}:00`}
                                            className="border-l border-border/40 transition-colors enabled:hover:bg-accent disabled:cursor-not-allowed"
                                        />
                                    ))}
                                </div>

                                {roomBookings.map((item) => {
                                    const start = timeToMinutes(
                                        item.startTime,
                                    );
                                    const end = timeToMinutes(item.endTime);
                                    const left =
                                        ((start - DAY_START_MINUTES) /
                                            DAY_SPAN_MINUTES) *
                                        100;
                                    const width =
                                        ((end - start) / DAY_SPAN_MINUTES) *
                                        100;

                                    return (
                                        <div
                                            key={item.id}
                                            title={`${item.user} · ${item.purpose} (${item.startTime}–${item.endTime})`}
                                            className={cn(
                                                'pointer-events-none absolute top-1 bottom-1 flex items-center overflow-hidden rounded px-2 text-xs font-medium text-white',
                                                item.status === 'approved'
                                                    ? 'bg-dkst-navy dark:bg-dkst-cyan-light dark:text-dkst-navy'
                                                    : 'border border-dashed border-dkst-navy bg-dkst-navy/20 text-dkst-navy dark:border-dkst-cyan-light dark:text-dkst-cyan-light',
                                            )}
                                            style={{
                                                left: `${left}%`,
                                                width: `${width}%`,
                                            }}
                                        >
                                            <span className="truncate">
                                                {item.user}
                                            </span>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
