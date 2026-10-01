import { Link, usePage } from '@inertiajs/react';
import { ArrowRight, Sparkles } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { dashboard, register } from '@/routes';

export default function HeroSection() {
    const { auth } = usePage().props;

    return (
        <section className="relative overflow-hidden">
            <div
                aria-hidden
                className="pointer-events-none absolute inset-x-0 -top-32 -z-10 h-96 bg-gradient-to-b from-dkst-navy/10 to-transparent dark:from-dkst-navy/20"
            />

            <div className="mx-auto grid max-w-6xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:gap-16 lg:px-8 lg:py-24">
                <div className="flex flex-col gap-6">
                    <span className="inline-flex w-fit items-center gap-1.5 rounded-full border border-dkst-cyan/30 bg-dkst-cyan/10 px-3 py-1 text-xs font-semibold tracking-wide text-dkst-navy uppercase dark:text-dkst-cyan-light">
                        Integrated • Intelligent • Collaborative
                    </span>

                    <h1 className="text-4xl font-bold tracking-tight text-balance sm:text-5xl">
                        Digital &amp; AI Platform{' '}
                        <span className="text-dkst-navy dark:text-dkst-cyan-light">
                            DKST
                        </span>
                    </h1>

                    <p className="max-w-xl text-lg text-balance text-muted-foreground">
                        Menghubungkan Data, Proses, dan Manusia untuk Inovasi
                        yang Berdampak.
                    </p>

                    <div className="flex flex-wrap items-center gap-3">
                        {auth.user ? (
                            <Button size="lg" asChild>
                                <Link href={dashboard()}>
                                    Ke Dashboard
                                    <ArrowRight />
                                </Link>
                            </Button>
                        ) : (
                            <Button size="lg" asChild>
                                <Link href={register()}>
                                    Mulai Sekarang
                                    <ArrowRight />
                                </Link>
                            </Button>
                        )}
                        <Button size="lg" variant="outline" asChild>
                            <a href="#layanan">Lihat Layanan</a>
                        </Button>
                    </div>
                </div>

                <div className="relative mx-auto w-full max-w-md lg:max-w-none">
                    <div
                        aria-hidden
                        className="absolute -top-6 -right-6 size-32 rounded-full bg-dkst-cyan/20 blur-2xl"
                    />
                    <div
                        aria-hidden
                        className="absolute -bottom-8 -left-6 size-40 rounded-full bg-dkst-navy/10 blur-2xl dark:bg-dkst-navy/30"
                    />

                    <Card className="relative gap-4 border-border/60 p-5 shadow-xl">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <span className="size-2.5 rounded-full bg-red-400" />
                                <span className="size-2.5 rounded-full bg-amber-400" />
                                <span className="size-2.5 rounded-full bg-emerald-400" />
                            </div>
                            <span className="inline-flex items-center gap-1 rounded-full bg-dkst-cyan/10 px-2 py-0.5 text-[11px] font-medium text-dkst-navy dark:text-dkst-cyan-light">
                                <Sparkles className="size-3" />
                                AI Insight
                            </span>
                        </div>

                        <div className="grid grid-cols-3 gap-3">
                            {[
                                { label: 'Program', value: '24' },
                                { label: 'Teknologi', value: '58' },
                                { label: 'Mitra', value: '32' },
                            ].map((item) => (
                                <div
                                    key={item.label}
                                    className="rounded-lg bg-muted p-3"
                                >
                                    <p className="text-xl font-bold">
                                        {item.value}
                                    </p>
                                    <p className="text-[11px] text-muted-foreground">
                                        {item.label}
                                    </p>
                                </div>
                            ))}
                        </div>

                        <div className="rounded-lg border border-border/60 p-4">
                            <svg
                                viewBox="0 0 280 90"
                                className="h-20 w-full text-dkst-navy dark:text-dkst-cyan-light"
                                fill="none"
                            >
                                <polyline
                                    points="0,70 40,55 80,60 120,35 160,42 200,18 240,25 280,8"
                                    stroke="currentColor"
                                    strokeWidth="3"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                />
                                <polyline
                                    points="0,70 40,55 80,60 120,35 160,42 200,18 240,25 280,8"
                                    stroke="currentColor"
                                    strokeWidth="3"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    className="text-dkst-cyan opacity-30"
                                    transform="translate(0 6)"
                                />
                            </svg>
                        </div>

                        <div className="flex items-center gap-2">
                            {['Monev', 'Inkubasi', 'Aset'].map((tag) => (
                                <span
                                    key={tag}
                                    className="rounded-full bg-secondary px-2.5 py-1 text-[11px] font-medium text-secondary-foreground"
                                >
                                    {tag}
                                </span>
                            ))}
                        </div>
                    </Card>
                </div>
            </div>
        </section>
    );
}
