// TODO: ganti dengan endpoint backend GET /search untuk menambah grup "Data"
// (hasil pencarian isi data seperti surat, program, atau teknologi tertentu).

export type QuickAction = {
    id: string;
    title: string;
    icon: string;
    permission: string;
    href?: string;
    action?: 'open-ai';
};

export const quickActions: QuickAction[] = [
    {
        id: 'booking-ruangan',
        title: 'Booking ruangan',
        icon: 'CalendarCheck',
        permission: 'ruangan.book',
        href: '/ruangan/booking/create',
    },
    {
        id: 'buat-surat',
        title: 'Buat surat',
        icon: 'Mail',
        permission: 'surat.manage',
        href: '/surat',
    },
    {
        id: 'tanya-ai',
        title: 'Tanya AI',
        icon: 'Sparkles',
        permission: 'ai.use',
        action: 'open-ai',
    },
];
