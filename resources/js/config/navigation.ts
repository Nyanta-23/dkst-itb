import type { InertiaLinkProps } from '@inertiajs/react';
import {
    Archive,
    Bot,
    BookOpen,
    Building2,
    CalendarCheck,
    CalendarClock,
    CalendarDays,
    CalendarPlus,
    ClipboardCheck,
    ClipboardList,
    FileCog,
    Handshake,
    LayoutGrid,
    Lightbulb,
    Mail,
    Rocket,
    Settings2,
    Users,
    type LucideIcon,
} from 'lucide-react';
import { dashboard } from '@/routes';
import ai from '@/routes/ai';
import arsip from '@/routes/arsip';
import aset from '@/routes/aset';
import inkubasi from '@/routes/inkubasi';
import knowledgeBase from '@/routes/knowledge-base';
import layananAdministrasi from '@/routes/layanan-administrasi';
import mitra from '@/routes/mitra';
import program from '@/routes/program';
import rapat from '@/routes/rapat';
import ruangan from '@/routes/ruangan';
import surat from '@/routes/surat';
import teknologi from '@/routes/teknologi';
import users from '@/routes/users';

export type NavigationStatus = 'available' | 'soon';

export type NavigationItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon: LucideIcon;
    permission: string;
    status: NavigationStatus;
};

export type NavigationGroup = {
    label: string;
    items: NavigationItem[];
};

/**
 * Satu sumber menu navigasi aplikasi (sidebar & command palette pencarian).
 * Setiap item difilter berdasarkan permission user yang sedang login.
 */
export const navigationGroups: NavigationGroup[] = [
    {
        label: 'Utama',
        items: [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: LayoutGrid,
                permission: 'dashboard.view',
                status: 'available',
            },
        ],
    },
    {
        label: 'Administrasi',
        items: [
            {
                title: 'Surat & Disposisi',
                href: surat.index(),
                icon: Mail,
                permission: 'surat.view',
                status: 'available',
            },
            {
                title: 'Arsip & Dokumen',
                href: arsip.index(),
                icon: Archive,
                permission: 'surat.view',
                status: 'soon',
            },
            {
                title: 'Rapat & Notulen',
                href: rapat.index(),
                icon: CalendarDays,
                permission: 'surat.view',
                status: 'soon',
            },
            {
                title: 'Layanan Administrasi',
                href: layananAdministrasi.index(),
                icon: FileCog,
                permission: 'surat.view',
                status: 'soon',
            },
        ],
    },
    {
        label: 'Program',
        items: [
            {
                title: 'Daftar Program',
                href: program.index(),
                icon: ClipboardList,
                permission: 'program.view',
                status: 'available',
            },
            {
                title: 'Verifikasi Realisasi',
                href: program.verifikasi(),
                icon: ClipboardCheck,
                permission: 'program.verify',
                status: 'available',
            },
        ],
    },
    {
        label: 'Inovasi',
        items: [
            {
                title: 'Teknologi & KI',
                href: teknologi.index(),
                icon: Lightbulb,
                permission: 'teknologi.view',
                status: 'soon',
            },
            {
                title: 'Mitra',
                href: mitra.index(),
                icon: Handshake,
                permission: 'mitra.view',
                status: 'soon',
            },
            {
                title: 'Inkubasi',
                href: inkubasi.index(),
                icon: Rocket,
                permission: 'inkubasi.view',
                status: 'soon',
            },
        ],
    },
    {
        label: 'Fasilitas',
        items: [
            {
                title: 'Aset',
                href: aset.index(),
                icon: Building2,
                permission: 'aset.view',
                status: 'soon',
            },
            {
                title: 'Jadwal Ruangan',
                href: ruangan.index(),
                icon: CalendarCheck,
                permission: 'ruangan.view',
                status: 'available',
            },
            {
                title: 'Booking Saya',
                href: ruangan.myBookings(),
                icon: CalendarPlus,
                permission: 'ruangan.book',
                status: 'available',
            },
            {
                title: 'Persetujuan Ruangan',
                href: ruangan.approvals.index(),
                icon: CalendarClock,
                permission: 'ruangan.approve',
                status: 'available',
            },
            {
                title: 'Kelola Ruangan',
                href: ruangan.manage.index(),
                icon: Settings2,
                permission: 'ruangan.manage',
                status: 'available',
            },
        ],
    },
    {
        label: 'AI',
        items: [
            {
                title: 'AI Assistant',
                href: ai.index(),
                icon: Bot,
                permission: 'ai.use',
                status: 'available',
            },
            {
                title: 'Knowledge Base',
                href: knowledgeBase.index(),
                icon: BookOpen,
                permission: 'ai.manage',
                status: 'soon',
            },
        ],
    },
    {
        label: 'Pengaturan',
        items: [
            {
                title: 'Manajemen User',
                href: users.index(),
                icon: Users,
                permission: 'admin.manage',
                status: 'soon',
            },
        ],
    },
];

/**
 * Filter grup navigasi berdasarkan permission user. Super-admin selalu melihat semua
 * menu. Grup yang tidak memiliki item yang boleh dilihat akan disembunyikan.
 */
export function getVisibleNavigationGroups(
    permissions: string[],
    roles: string[],
): NavigationGroup[] {
    const isSuperAdmin = roles.includes('super-admin');

    return navigationGroups
        .map((group) => ({
            ...group,
            items: group.items.filter(
                (item) => isSuperAdmin || permissions.includes(item.permission),
            ),
        }))
        .filter((group) => group.items.length > 0);
}

/** Semua item navigasi dalam satu daftar flat, untuk dipakai command palette. */
export function getAllNavigationItems(): NavigationItem[] {
    return navigationGroups.flatMap((group) => group.items);
}
