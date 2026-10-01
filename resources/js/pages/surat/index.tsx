import { Head, Link, router } from '@inertiajs/react';
import { FilePlus2, Search } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { Pagination } from '@/components/pagination';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import Heading from '@/components/heading';
import { formatStatusLabel } from '@/lib/status-labels';
import { cn } from '@/lib/utils';
import { create, index, show } from '@/routes/surat';
import type { Paginated } from '@/types/ui';

type LetterRow = {
    id: number;
    type: 'incoming' | 'outgoing';
    letterNumber: string;
    subject: string;
    sender: string;
    recipient: string;
    letterDate: string;
    classification: string;
    status: string;
    creator: string;
};

type Filters = {
    tab: string;
    search: string;
    status: string;
    classification: string;
};

type Props = {
    letters: Paginated<LetterRow>;
    filters: Filters;
    can: { manage: boolean };
};

const TABS = [
    { value: 'all', label: 'Semua' },
    { value: 'incoming', label: 'Surat Masuk' },
    { value: 'outgoing', label: 'Surat Keluar' },
    { value: 'my-dispositions', label: 'Disposisi untuk Saya' },
];

const CLASSIFICATION_BADGE: Record<string, 'secondary' | 'outline' | 'destructive'> = {
    regular: 'secondary',
    important: 'outline',
    confidential: 'destructive',
};

export default function LetterIndex({ letters, filters, can }: Props) {
    const [search, setSearch] = useState(filters.search);
    const isFirstRender = useRef(true);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            return;
        }

        const timeout = setTimeout(() => {
            navigate({ search });
        }, 400);

        return () => clearTimeout(timeout);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    function navigate(overrides: Partial<Filters>) {
        router.get(
            index().url,
            { ...filters, ...overrides },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }

    return (
        <>
            <Head title="Surat & Disposisi" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Surat & Disposisi"
                        description="Kelola surat masuk, surat keluar, dan disposisi ke unit terkait."
                    />
                    {can.manage && (
                        <Button asChild>
                            <Link href={create()}>
                                <FilePlus2 />
                                Catat Surat
                            </Link>
                        </Button>
                    )}
                </div>

                <div className="flex flex-wrap gap-2">
                    {TABS.map((tab) => (
                        <Button
                            key={tab.value}
                            size="sm"
                            variant={
                                filters.tab === tab.value
                                    ? 'default'
                                    : 'outline'
                            }
                            onClick={() => navigate({ tab: tab.value })}
                        >
                            {tab.label}
                        </Button>
                    ))}
                </div>

                <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div className="relative flex-1">
                        <Search className="absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Cari nomor surat, perihal, atau pengirim…"
                            className="pl-8"
                        />
                    </div>

                    <Select
                        value={filters.status || 'all'}
                        onValueChange={(value) =>
                            navigate({ status: value === 'all' ? '' : value })
                        }
                    >
                        <SelectTrigger className="sm:w-48">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Status</SelectItem>
                            <SelectItem value="new">Baru</SelectItem>
                            <SelectItem value="forwarded">
                                Didisposisikan
                            </SelectItem>
                            <SelectItem value="completed">Selesai</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        value={filters.classification || 'all'}
                        onValueChange={(value) =>
                            navigate({
                                classification: value === 'all' ? '' : value,
                            })
                        }
                    >
                        <SelectTrigger className="sm:w-48">
                            <SelectValue placeholder="Sifat" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Sifat</SelectItem>
                            <SelectItem value="regular">Biasa</SelectItem>
                            <SelectItem value="important">Penting</SelectItem>
                            <SelectItem value="confidential">
                                Rahasia
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <Card className="overflow-hidden border-border/60 p-0 shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b bg-muted/40 text-left text-xs font-medium text-muted-foreground uppercase">
                                <tr>
                                    <th className="px-4 py-3">Nomor Surat</th>
                                    <th className="px-4 py-3">Perihal</th>
                                    <th className="px-4 py-3">
                                        Pengirim/Penerima
                                    </th>
                                    <th className="px-4 py-3">Tanggal</th>
                                    <th className="px-4 py-3">Sifat</th>
                                    <th className="px-4 py-3">Status</th>
                                    <th className="px-4 py-3">Dicatat Oleh</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {letters.data.map((letter) => (
                                    <tr
                                        key={letter.id}
                                        onClick={() =>
                                            router.visit(show(letter.id))
                                        }
                                        className="cursor-pointer hover:bg-accent"
                                    >
                                        <td className="px-4 py-3 font-medium whitespace-nowrap">
                                            {letter.letterNumber}
                                        </td>
                                        <td className="max-w-xs truncate px-4 py-3">
                                            {letter.subject}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                            {letter.type === 'incoming'
                                                ? letter.sender
                                                : letter.recipient}
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                            {letter.letterDate}
                                        </td>
                                        <td className="px-4 py-3">
                                            <Badge
                                                variant={
                                                    CLASSIFICATION_BADGE[
                                                        letter.classification
                                                    ]
                                                }
                                            >
                                                {formatStatusLabel(
                                                    letter.classification,
                                                )}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <Badge variant="secondary">
                                                {formatStatusLabel(
                                                    letter.status,
                                                )}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                            {letter.creator}
                                        </td>
                                    </tr>
                                ))}

                                {letters.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={7}
                                            className={cn(
                                                'px-4 py-10 text-center text-muted-foreground',
                                            )}
                                        >
                                            Tidak ada surat yang ditemukan.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </Card>

                <Pagination paginated={letters} />
            </div>
        </>
    );
}

LetterIndex.layout = () => ({
    breadcrumbs: [{ title: 'Surat & Disposisi', href: index() }],
});
