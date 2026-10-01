import {
    BarChart3,
    HeartHandshake,
    Network,
    ShieldCheck,
    TrendingUp,
    Zap,
} from 'lucide-react';

const benefits = [
    {
        icon: Zap,
        title: 'Proses Lebih Efisien',
        description:
            'Mengurangi pekerjaan manual dan mempercepat alur kerja antar unit.',
    },
    {
        icon: BarChart3,
        title: 'Keputusan Berbasis Data',
        description:
            'Data program dan teknologi tersedia secara real-time untuk pengambilan keputusan.',
    },
    {
        icon: HeartHandshake,
        title: 'Layanan Lebih Baik',
        description:
            'Mitra dan startup binaan mendapat layanan yang lebih cepat dan transparan.',
    },
    {
        icon: TrendingUp,
        title: 'Komersialisasi Meningkat',
        description:
            'Mempermudah proses lisensi dan hilirisasi teknologi hasil riset.',
    },
    {
        icon: ShieldCheck,
        title: 'Tata Kelola Transparan',
        description:
            'Setiap proses dan persetujuan tercatat dan dapat ditelusuri.',
    },
    {
        icon: Network,
        title: 'Ekosistem Inovasi',
        description:
            'Menghubungkan unit internal, mitra, dan startup dalam satu ekosistem.',
    },
];

export default function BenefitsSection() {
    return (
        <section
            id="manfaat"
            className="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8"
        >
            <div className="mx-auto max-w-2xl text-center">
                <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
                    Manfaat
                </h2>
                <p className="mt-3 text-muted-foreground">
                    Dampak nyata yang dihadirkan platform ini bagi DKST dan
                    seluruh mitra kerjanya.
                </p>
            </div>

            <div className="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {benefits.map(({ icon: Icon, title, description }) => (
                    <div key={title} className="flex flex-col gap-3">
                        <span className="flex size-10 items-center justify-center rounded-lg bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                            <Icon className="size-5" />
                        </span>
                        <h3 className="font-semibold">{title}</h3>
                        <p className="text-sm text-muted-foreground">
                            {description}
                        </p>
                    </div>
                ))}
            </div>
        </section>
    );
}
