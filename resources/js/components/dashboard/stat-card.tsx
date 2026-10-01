import { Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import { Card } from '@/components/ui/card';
import { formatNumber } from '@/lib/format';

export function StatCard({
    label,
    value,
    icon: Icon,
    href,
}: {
    label: string;
    value: number;
    icon: LucideIcon;
    href?: string;
}) {
    const content = (
        <>
            <span className="flex size-11 shrink-0 items-center justify-center rounded-xl bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                <Icon className="size-5" />
            </span>
            <div>
                <p className="text-2xl font-bold tabular-nums">
                    {formatNumber(value)}
                </p>
                <p className="text-xs text-muted-foreground">{label}</p>
            </div>
        </>
    );

    if (href) {
        return (
            <Link href={href} className="block">
                <Card className="flex-row items-center gap-4 border-border/60 p-4 shadow-sm transition-colors hover:bg-accent">
                    {content}
                </Card>
            </Link>
        );
    }

    return (
        <Card className="flex-row items-center gap-4 border-border/60 p-4 shadow-sm">
            {content}
        </Card>
    );
}
