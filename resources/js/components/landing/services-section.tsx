import {
    Bot,
    Building2,
    ClipboardList,
    Handshake,
    Lightbulb,
    Mail,
    Rocket,
    Wallet,
} from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const services = [
    {
        icon: ClipboardList,
        name: 'Program & Monev',
        description:
            'Kelola program kerja, indikator kinerja (IKU), dan realisasi secara terintegrasi.',
        available: true,
    },
    {
        icon: Lightbulb,
        name: 'Teknologi & KI',
        description:
            'Katalog teknologi hasil riset beserta status kekayaan intelektualnya.',
        available: true,
    },
    {
        icon: Handshake,
        name: 'Mitra & Kerja Sama',
        description:
            'Kelola data mitra industri, pemerintah, dan transaksi kerja sama.',
        available: true,
    },
    {
        icon: Rocket,
        name: 'Inkubasi & Startup',
        description:
            'Pantau progres startup binaan dari pra-inkubasi hingga akselerasi.',
        available: true,
    },
    {
        icon: Building2,
        name: 'Aset & Ruangan',
        description:
            'Inventaris aset dan pemesanan ruangan dengan alur persetujuan.',
        available: true,
    },
    {
        icon: Mail,
        name: 'Surat & Disposisi',
        description: 'Pencatatan surat masuk/keluar beserta alur disposisinya.',
        available: true,
    },
    {
        icon: Bot,
        name: 'AI Assistant',
        description:
            'Asisten cerdas untuk pencarian informasi dan bantuan kerja sehari-hari.',
        available: true,
    },
    {
        icon: Wallet,
        name: 'Keuangan',
        description:
            'Perincian anggaran program dan pengajuan pencairan, terhubung ke Oracle Fusion.',
        available: false,
    },
];

export default function ServicesSection() {
    return (
        <section
            id="layanan"
            className="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8"
        >
            <div className="mx-auto max-w-2xl text-center">
                <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
                    Layanan
                </h2>
                <p className="mt-3 text-muted-foreground">
                    Delapan modul yang mendukung seluruh proses kerja DKST, dari
                    riset hingga komersialisasi.
                </p>
            </div>

            <div className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {services.map(
                    ({ icon: Icon, name, description, available }) => (
                        <Card key={name} className="gap-3">
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <span className="flex size-10 items-center justify-center rounded-lg bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                                        <Icon className="size-5" />
                                    </span>
                                    <Badge
                                        variant={
                                            available ? 'default' : 'secondary'
                                        }
                                    >
                                        {available ? 'Tersedia' : 'Segera'}
                                    </Badge>
                                </div>
                                <CardTitle className="mt-2 text-base">
                                    {name}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-sm text-muted-foreground">
                                    {description}
                                </p>
                            </CardContent>
                        </Card>
                    ),
                )}
            </div>
        </section>
    );
}
