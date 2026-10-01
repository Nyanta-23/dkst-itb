import { Head, usePage } from "@inertiajs/react";
import {
    Building2,
    CalendarClock,
    ClipboardList,
    Handshake,
    Lightbulb,
    Mail,
    Rocket,
    ShieldCheck,
    type LucideIcon,
} from "lucide-react";
import {
    IkuProgressCard,
    type IkuProgressItem,
} from "@/components/dashboard/iku-progress-card";
import {
    MyBookingsCard,
    type MyBookings,
} from "@/components/dashboard/my-bookings-card";
import {
    MyTasksCard,
    type MyTasks,
} from "@/components/dashboard/my-tasks-card";
import { StatCard } from "@/components/dashboard/stat-card";
import { formatIndonesianDate } from "@/lib/format";
import { dashboard } from "@/routes";
import ruangan from "@/routes/ruangan";

type Stats = {
    programsOngoing?: number;
    technologies?: number;
    ipAssetsGranted?: number;
    partners?: number;
    tenantsActive?: number;
    bookingsPending?: number;
    lettersOpen?: number;
};

type Props = {
    stats: Stats;
    ikuProgress: IkuProgressItem[] | null;
    myTasks: MyTasks | null;
    myBookings: MyBookings | null;
    can: { approveBookings: boolean };
};

const STAT_CONFIG: Record<keyof Stats, { label: string; icon: LucideIcon }> = {
    programsOngoing: { label: "Program Berjalan", icon: ClipboardList },
    technologies: { label: "Teknologi", icon: Lightbulb },
    ipAssetsGranted: { label: "KI Granted", icon: ShieldCheck },
    partners: { label: "Mitra", icon: Handshake },
    tenantsActive: { label: "Tenant Aktif", icon: Rocket },
    bookingsPending: { label: "Booking Menunggu", icon: CalendarClock },
    lettersOpen: { label: "Surat Belum Selesai", icon: Mail },
};

function getGreeting(): string {
    const hour = new Date().getHours();

    if (hour < 11) {
        return "Selamat pagi";
    }

    if (hour < 15) {
        return "Selamat siang";
    }

    if (hour < 19) {
        return "Selamat sore";
    }

    return "Selamat malam";
}

export default function Dashboard({
    stats,
    ikuProgress,
    myTasks,
    myBookings,
    can,
}: Props) {
    const { auth } = usePage().props;
    const statEntries = Object.entries(stats) as [keyof Stats, number][];

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-1">
                    <h1 className="text-2xl font-semibold">
                        {getGreeting()}, {auth.user.name.split(" ")[0]}
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        {auth.user.unit ? `${auth.user.unit.name} · ` : ""}
                        {formatIndonesianDate()}
                    </p>
                </div>

                {statEntries.length > 0 && (
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        {statEntries.map(([key, value]) => {
                            const config = STAT_CONFIG[key];
                            const href =
                                key === "bookingsPending" &&
                                can.approveBookings
                                    ? ruangan.approvals.index().url
                                    : undefined;

                            return (
                                <StatCard
                                    key={key}
                                    label={config.label}
                                    icon={config.icon}
                                    value={value}
                                    href={href}
                                />
                            );
                        })}
                    </div>
                )}

                {ikuProgress && ikuProgress.length > 0 && (
                    <IkuProgressCard items={ikuProgress} />
                )}

                <div className="grid gap-4 lg:grid-cols-2">
                    {myTasks && <MyTasksCard tasks={myTasks} />}
                    {myBookings && <MyBookingsCard data={myBookings} />}
                </div>

                {statEntries.length === 0 &&
                    !ikuProgress &&
                    !myTasks &&
                    !myBookings && (
                        <div className="flex flex-1 items-center justify-center rounded-xl border border-dashed border-border/60 p-10 text-center text-sm text-muted-foreground">
                            <div className="flex items-center gap-2">
                                <Building2 className="size-4" />
                                Belum ada data yang bisa ditampilkan untuk akun
                                ini.
                            </div>
                        </div>
                    )}
            </div>
        </>
    );
}

Dashboard.layout = () => ({
    breadcrumbs: [{ title: "Dashboard", href: dashboard() }],
});
