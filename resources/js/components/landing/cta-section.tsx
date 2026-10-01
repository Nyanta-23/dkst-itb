import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { register } from '@/routes';

export default function CtaSection() {
    return (
        <section className="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <div className="relative overflow-hidden rounded-2xl bg-gradient-to-br from-dkst-navy to-dkst-navy-deep px-6 py-14 text-center text-white sm:px-12">
                <div
                    aria-hidden
                    className="pointer-events-none absolute -top-16 -right-16 size-64 rounded-full bg-dkst-cyan/20 blur-3xl"
                />
                <h2 className="relative text-3xl font-bold tracking-tight text-balance sm:text-4xl">
                    Inovasi Hari Ini untuk Masa Depan yang Lebih Besar
                </h2>
                <p className="relative mx-auto mt-3 max-w-xl text-white/70">
                    Bergabunglah sebagai mitra, startup, atau pengguna eksternal
                    dan mulai berkolaborasi dengan DKST ITB.
                </p>
                <Button size="lg" className="relative mt-8" asChild>
                    <Link href={register()}>
                        Daftar Sekarang
                        <ArrowRight />
                    </Link>
                </Button>
            </div>
        </section>
    );
}
