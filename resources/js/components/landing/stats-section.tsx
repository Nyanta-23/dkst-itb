import { Building2, Lightbulb, Rocket, ShieldCheck } from 'lucide-react';

export type LandingStats = {
    technologies: number;
    ipAssets: number;
    partners: number;
    tenants: number;
};

export default function StatsSection({ stats }: { stats: LandingStats }) {
    const items = [
        {
            icon: Lightbulb,
            value: stats.technologies,
            label: 'Teknologi Terdaftar',
        },
        {
            icon: ShieldCheck,
            value: stats.ipAssets,
            label: 'Kekayaan Intelektual',
        },
        {
            icon: Building2,
            value: stats.partners,
            label: 'Mitra Kerja Sama',
        },
        {
            icon: Rocket,
            value: stats.tenants,
            label: 'Startup Binaan',
        },
    ];

    return (
        <section className="border-y border-border/60 bg-muted/40">
            <div className="mx-auto grid max-w-6xl grid-cols-2 gap-6 px-4 py-10 sm:px-6 lg:grid-cols-4 lg:px-8">
                {items.map(({ icon: Icon, value, label }) => (
                    <div
                        key={label}
                        className="flex flex-col items-center gap-2 text-center"
                    >
                        <span className="flex size-10 items-center justify-center rounded-full bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                            <Icon className="size-5" />
                        </span>
                        <p className="text-3xl font-bold tabular-nums">
                            {value}
                        </p>
                        <p className="text-sm text-muted-foreground">{label}</p>
                    </div>
                ))}
            </div>
        </section>
    );
}
