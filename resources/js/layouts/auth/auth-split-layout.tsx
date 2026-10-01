import { Link } from '@inertiajs/react';
import { BrainCircuit, Handshake, LineChart } from 'lucide-react';
import DkstWordmark from '@/components/dkst-wordmark';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

const highlights = [
    {
        icon: LineChart,
        text: 'Satu platform untuk program, teknologi, dan kekayaan intelektual DKST.',
    },
    {
        icon: Handshake,
        text: 'Kolaborasi mitra, startup, dan unit internal dalam satu alur kerja.',
    },
    {
        icon: BrainCircuit,
        text: 'Didukung AI Assistant untuk pencarian dan analisis yang lebih cepat.',
    },
];

export default function AuthSplitLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div className="relative hidden h-full flex-col justify-between overflow-hidden bg-gradient-to-br from-dkst-navy to-dkst-navy-deep p-10 text-white lg:flex">
                <div
                    aria-hidden
                    className="pointer-events-none absolute -top-24 -left-24 size-72 rounded-full bg-dkst-cyan/20 blur-3xl"
                />

                <Link href={home()} className="relative z-20">
                    <DkstWordmark variant="light" className="text-lg" />
                </Link>

                <div className="relative z-20 flex flex-col gap-6">
                    <div>
                        <p className="text-xs font-medium tracking-[0.2em] text-dkst-cyan-light uppercase">
                            Digital &amp; AI Platform
                        </p>
                        <h2 className="mt-2 text-2xl font-semibold text-balance">
                            Menghubungkan Data, Proses, dan Manusia untuk
                            Inovasi yang Berdampak
                        </h2>
                    </div>

                    <ul className="flex flex-col gap-4">
                        {highlights.map(({ icon: Icon, text }) => (
                            <li
                                key={text}
                                className="flex items-start gap-3 text-sm text-white/80"
                            >
                                <span className="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-white/10">
                                    <Icon className="size-4 text-dkst-cyan-light" />
                                </span>
                                <span>{text}</span>
                            </li>
                        ))}
                    </ul>
                </div>

                <p className="relative z-20 text-xs text-white/50">
                    Direktorat Kawasan Sains dan Teknologi ITB
                </p>
            </div>
            <div className="w-full lg:p-8">
                <div className="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <Link
                        href={home()}
                        className="relative z-20 flex items-center justify-center lg:hidden"
                    >
                        <DkstWordmark className="text-xl" />
                    </Link>
                    <div className="flex flex-col items-start gap-2 text-left sm:items-center sm:text-center">
                        <h1 className="text-xl font-medium">{title}</h1>
                        <p className="text-sm text-balance text-muted-foreground">
                            {description}
                        </p>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
