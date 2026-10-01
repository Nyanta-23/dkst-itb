import { Head, router } from '@inertiajs/react';
import * as LucideIcons from 'lucide-react';
import { Bell, type LucideIcon } from 'lucide-react';
import { Pagination } from '@/components/pagination';
import { Card } from '@/components/ui/card';
import { markRead } from '@/routes/notifications';
import { formatRelativeTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { AppNotification, Paginated } from '@/types/ui';

type Props = {
    notifications: Paginated<AppNotification>;
};

export default function Notifications({ notifications }: Props) {
    const icons = LucideIcons as unknown as Record<string, LucideIcon>;

    function handleItemClick(notification: AppNotification) {
        router.post(markRead.url(notification.id));
    }

    return (
        <>
            <Head title="Notifikasi" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-xl font-semibold">Notifikasi</h1>
                    <p className="text-sm text-muted-foreground">
                        Semua notifikasi yang relevan dengan akun Anda.
                    </p>
                </div>

                <Card className="divide-y divide-border p-0">
                    {notifications.data.length === 0 && (
                        <p className="p-6 text-center text-sm text-muted-foreground">
                            Belum ada notifikasi.
                        </p>
                    )}
                    {notifications.data.map((notification) => {
                        const Icon = icons[notification.data.icon] ?? Bell;

                        return (
                            <button
                                key={notification.id}
                                type="button"
                                onClick={() => handleItemClick(notification)}
                                className={cn(
                                    'flex w-full items-start gap-3 px-4 py-4 text-left text-sm hover:bg-accent',
                                    !notification.read_at && 'bg-dkst-cyan/5',
                                )}
                            >
                                <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                                    <Icon className="size-5" />
                                </span>
                                <span className="flex-1 space-y-1">
                                    <span className="flex items-center gap-2 font-medium">
                                        {notification.data.title}
                                        {!notification.read_at && (
                                            <span className="size-1.5 rounded-full bg-dkst-cyan" />
                                        )}
                                    </span>
                                    <span className="block text-muted-foreground">
                                        {notification.data.message}
                                    </span>
                                    <span className="block text-xs text-muted-foreground">
                                        {formatRelativeTime(
                                            notification.created_at,
                                        )}
                                    </span>
                                </span>
                            </button>
                        );
                    })}
                </Card>

                <Pagination paginated={notifications} />
            </div>
        </>
    );
}

Notifications.layout = () => ({
    breadcrumbs: [{ title: 'Notifikasi', href: '#' }],
});
