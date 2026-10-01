import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import DkstWordmark from '@/components/dkst-wordmark';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { dashboard, login, register } from '@/routes';

const navLinks = [
    { href: '#layanan', label: 'Layanan' },
    { href: '#ai', label: 'AI' },
    { href: '#untuk-siapa', label: 'Untuk Siapa' },
    { href: '#manfaat', label: 'Manfaat' },
];

export default function SiteNavbar() {
    const { auth } = usePage().props;

    return (
        <header className="sticky top-0 z-40 border-b border-border/60 bg-background/80 backdrop-blur-md">
            <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="#" className="flex items-center">
                    <DkstWordmark />
                </Link>

                <nav className="hidden items-center gap-8 md:flex">
                    {navLinks.map((link) => (
                        <a
                            key={link.href}
                            href={link.href}
                            className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {link.label}
                        </a>
                    ))}
                </nav>

                <div className="hidden items-center gap-3 md:flex">
                    {auth.user ? (
                        <Button asChild>
                            <Link href={dashboard()}>Ke Dashboard</Link>
                        </Button>
                    ) : (
                        <>
                            <Button variant="ghost" asChild>
                                <Link href={login()}>Masuk</Link>
                            </Button>
                            <Button asChild>
                                <Link href={register()}>Daftar</Link>
                            </Button>
                        </>
                    )}
                </div>

                <Sheet>
                    <SheetTrigger asChild>
                        <Button
                            variant="ghost"
                            size="icon"
                            className="md:hidden"
                            aria-label="Buka menu"
                        >
                            <Menu />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="right" className="w-72">
                        <SheetHeader>
                            <SheetTitle>
                                <DkstWordmark />
                            </SheetTitle>
                        </SheetHeader>
                        <nav className="flex flex-col gap-4 px-4">
                            {navLinks.map((link) => (
                                <a
                                    key={link.href}
                                    href={link.href}
                                    className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {link.label}
                                </a>
                            ))}
                        </nav>
                        <div className="mt-4 flex flex-col gap-2 px-4">
                            {auth.user ? (
                                <Button asChild>
                                    <Link href={dashboard()}>Ke Dashboard</Link>
                                </Button>
                            ) : (
                                <>
                                    <Button variant="outline" asChild>
                                        <Link href={login()}>Masuk</Link>
                                    </Button>
                                    <Button asChild>
                                        <Link href={register()}>Daftar</Link>
                                    </Button>
                                </>
                            )}
                        </div>
                    </SheetContent>
                </Sheet>
            </div>
        </header>
    );
}
