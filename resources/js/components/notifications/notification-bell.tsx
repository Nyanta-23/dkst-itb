import { Link } from '@inertiajs/react';
import { Bell, type LucideIcon } from 'lucide-react';
import * as LucideIcons from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useNotifications } from '@/hooks/use-notifications';
import { formatRelativeTime } from '@/lib/format';
import { cn } from '@/lib/utils';

export function NotificationBell() {
    const { items, unreadCount, handleItemClick, markAllAsRead } =
        useNotifications();
    const icons = LucideIcons as unknown as Record<string, LucideIcon>;

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    className="relative"
                    aria-label="Notifikasi"
                >
                    <Bell />
                    {unreadCount > 0 && (
                        <span className="absolute top-1 right-1 flex size-4 items-center justify-center rounded-full bg-dkst-cyan text-[10px] font-semibold text-dkst-navy">
                            {unreadCount}
                        </span>
                    )}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-80 p-0">
                <div className="flex items-center justify-between px-3 py-2">
                    <span className="text-sm font-semibold">Notifikasi</span>
                    <button
                        type="button"
                        onClick={markAllAsRead}
                        className="text-xs text-muted-foreground hover:text-foreground"
                    >
                        Tandai semua dibaca
                    </button>
                </div>
                <ScrollArea className="h-80">
                    <div className="flex flex-col">
                        {items.map((notification) => {
                            const Icon = icons[notification.data.icon] ?? Bell;

                            return (
                                <button
                                    key={notification.id}
                                    type="button"
                                    onClick={() =>
                                        handleItemClick(notification)
                                    }
                                    className={cn(
                                        'flex w-full items-start gap-3 border-b px-3 py-3 text-left text-sm last:border-0 hover:bg-accent',
                                        !notification.read_at &&
                                            'bg-dkst-cyan/5',
                                    )}
                                >
                                    <span className="flex size-8 shrink-0 items-center justify-center rounded-full bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                                        <Icon className="size-4" />
                                    </span>
                                    <span className="flex-1 space-y-0.5">
                                        <span className="flex items-center gap-2 font-medium">
                                            {notification.data.title}
                                            {!notification.read_at && (
                                                <span className="size-1.5 rounded-full bg-dkst-cyan" />
                                            )}
                                        </span>
                                        <span className="block text-xs text-muted-foreground">
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
                    </div>
                </ScrollArea>
                <Link
                    href="/notifikasi"
                    className="block border-t px-3 py-2 text-center text-sm font-medium text-dkst-navy hover:underline dark:text-dkst-cyan-light"
                >
                    Lihat semua
                </Link>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
