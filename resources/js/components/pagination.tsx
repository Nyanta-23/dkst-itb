import { Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { Paginated } from '@/types/ui';

type Props = {
    paginated: Pick<Paginated<unknown>, 'links' | 'from' | 'to' | 'total'>;
};

/** Navigasi pagination server-side generik, mengikuti bentuk LengthAwarePaginator. */
export function Pagination({ paginated }: Props) {
    if (paginated.links.length <= 3) {
        return null;
    }

    return (
        <div className="flex flex-col items-center justify-between gap-3 sm:flex-row">
            <p className="text-sm text-muted-foreground">
                {paginated.total > 0
                    ? `Menampilkan ${paginated.from}–${paginated.to} dari ${paginated.total} data`
                    : 'Tidak ada data'}
            </p>
            <div className="flex flex-wrap items-center gap-1">
                {paginated.links.map((link, index) => (
                    <Button
                        key={`${link.label}-${index}`}
                        asChild={link.url !== null}
                        variant={link.active ? 'default' : 'outline'}
                        size="sm"
                        disabled={link.url === null}
                        className={cn(
                            'min-w-9',
                            link.url === null && 'pointer-events-none opacity-50',
                        )}
                    >
                        {link.url !== null ? (
                            <Link
                                href={link.url}
                                preserveScroll
                                dangerouslySetInnerHTML={{
                                    __html: link.label,
                                }}
                            />
                        ) : (
                            <span
                                dangerouslySetInnerHTML={{
                                    __html: link.label,
                                }}
                            />
                        )}
                    </Button>
                ))}
            </div>
        </div>
    );
}
