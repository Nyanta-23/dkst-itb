// TODO: ganti dengan endpoint backend GET /notifications
// Bentuk data mengikuti DatabaseNotification bawaan Laravel.

export type NotificationData = {
    title: string;
    message: string;
    url: string;
    icon: string;
};

export type AppNotification = {
    id: string;
    type: string;
    data: NotificationData;
    read_at: string | null;
    created_at: string;
};

const minutesAgo = (minutes: number) =>
    new Date(Date.now() - minutes * 60_000).toISOString();

export const mockNotifications: AppNotification[] = [
    {
        id: "1",
        type: "disposition.created",
        data: {
            title: "Disposisi baru dari Direktur",
            message:
                "Mohon ditindaklanjuti dan dikoordinasikan dengan tim terkait.",
            url: "/surat",
            icon: "Mail",
        },
        read_at: null,
        created_at: minutesAgo(12),
    },
    {
        id: "2",
        type: "room_booking.approved",
        data: {
            title: "Booking ruangan disetujui",
            message:
                'Pemesanan "Ruang Diskusi Inkubasi" untuk 15 Oktober telah disetujui.',
            url: "/ruangan",
            icon: "CalendarCheck",
        },
        read_at: null,
        created_at: minutesAgo(45),
    },
    {
        id: "3",
        type: "indicator_realization.pending",
        data: {
            title: "Realisasi Q2 menunggu verifikasi",
            message:
                'Realisasi indikator "Jumlah startup lulus inkubasi" menunggu verifikasi Anda.',
            url: "/program",
            icon: "ClipboardList",
        },
        read_at: null,
        created_at: minutesAgo(130),
    },
    {
        id: "4",
        type: "technology.review",
        data: {
            title: "Teknologi baru menunggu review",
            message:
                '"Platform Analitik Data Pertanian" menunggu review status komersialisasi.',
            url: "/teknologi",
            icon: "Lightbulb",
        },
        read_at: minutesAgo(400),
        created_at: minutesAgo(500),
    },
    {
        id: "5",
        type: "partner.registered",
        data: {
            title: "Mitra baru mendaftar kerja sama",
            message: '"Koperasi Mitra Kampus" mengajukan kerja sama baru.',
            url: "/mitra",
            icon: "Handshake",
        },
        read_at: minutesAgo(1200),
        created_at: minutesAgo(1440),
    },
    {
        id: "6",
        type: "tenant.stage_changed",
        data: {
            title: "Startup binaan naik tahap akselerasi",
            message: '"EcoPack Indonesia" naik ke tahap akselerasi.',
            url: "/inkubasi",
            icon: "Rocket",
        },
        read_at: minutesAgo(2000),
        created_at: minutesAgo(2880),
    },
];
