import DkstWordmark from '@/components/dkst-wordmark';

export default function SiteFooter() {
    const year = new Date().getFullYear();

    return (
        <footer className="border-t border-border/60">
            <div className="mx-auto flex max-w-6xl flex-col items-center gap-3 px-4 py-10 text-center sm:px-6 lg:px-8">
                <DkstWordmark />
                <p className="text-sm font-medium">
                    Direktorat Kawasan Sains dan Teknologi ITB
                </p>
                <p className="text-sm text-muted-foreground">
                    Jl. Ganesha No. 10, Bandung, Jawa Barat 40132, Indonesia
                </p>
                <p className="text-xs text-muted-foreground">
                    &copy; {year} DKST ITB. Hak cipta dilindungi.
                </p>
            </div>
        </footer>
    );
}
