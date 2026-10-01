import { Building2, Globe } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const audiences = [
    {
        icon: Building2,
        name: 'Unit Internal DKST',
        description:
            'Direktorat, sekretariat, deputi, dan subdit yang menjalankan operasional sehari-hari.',
        examples: [
            'Direktur memantau capaian program dan menyetujui disposisi surat.',
            'Deputi mengelola teknologi, mitra, dan proses inkubasi sesuai bidangnya.',
            'Staf melaporkan realisasi indikator dan mengajukan peminjaman ruangan.',
        ],
    },
    {
        icon: Globe,
        name: 'Pengguna Eksternal',
        description:
            'Mitra industri, startup binaan, dan instansi pemerintah yang berkolaborasi dengan DKST.',
        examples: [
            'Mitra industri menjalin kerja sama lisensi dan komersialisasi teknologi.',
            'Startup binaan memantau progres inkubasi dan mengajukan peminjaman ruangan.',
            'Instansi pemerintah berkolaborasi dalam program dan kemitraan strategis.',
        ],
    },
];

export default function AudienceSection() {
    return (
        <section
            id="untuk-siapa"
            className="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8"
        >
            <div className="mx-auto max-w-2xl text-center">
                <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
                    Untuk Siapa
                </h2>
                <p className="mt-3 text-muted-foreground">
                    Dirancang untuk mendukung kolaborasi antara unit internal
                    DKST dan mitra di luar kampus.
                </p>
            </div>

            <div className="mt-12 grid gap-6 sm:grid-cols-2">
                {audiences.map(
                    ({ icon: Icon, name, description, examples }) => (
                        <Card key={name}>
                            <CardHeader>
                                <span className="flex size-10 items-center justify-center rounded-lg bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                                    <Icon className="size-5" />
                                </span>
                                <CardTitle className="mt-2">{name}</CardTitle>
                                <p className="text-sm text-muted-foreground">
                                    {description}
                                </p>
                            </CardHeader>
                            <CardContent>
                                <ul className="flex flex-col gap-2">
                                    {examples.map((example) => (
                                        <li
                                            key={example}
                                            className="flex gap-2 text-sm text-muted-foreground"
                                        >
                                            <span className="mt-2 size-1.5 shrink-0 rounded-full bg-dkst-cyan" />
                                            {example}
                                        </li>
                                    ))}
                                </ul>
                            </CardContent>
                        </Card>
                    ),
                )}
            </div>
        </section>
    );
}
