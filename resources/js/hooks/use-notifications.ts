import { router, usePage } from '@inertiajs/react';
import { markAllRead, markRead } from '@/routes/notifications';
// TODO: hapus mock ini setelah seluruh akun demo punya riwayat notifikasi asli.
import { mockNotifications } from '@/data/mock/notifications';
import type { AppNotification } from '@/types/ui';

export function useNotifications(): {
    items: AppNotification[];
    unreadCount: number;
    handleItemClick: (notification: AppNotification) => void;
    markAllAsRead: () => void;
} {
    const { notificationsSummary } = usePage().props;

    const usingMock =
        !notificationsSummary || notificationsSummary.recent.length === 0;
    const items = usingMock ? mockNotifications : notificationsSummary.recent;
    const unreadCount = usingMock
        ? items.filter((item) => !item.read_at).length
        : notificationsSummary.unreadCount;

    function handleItemClick(notification: AppNotification) {
        if (usingMock) {
            router.visit(notification.data.url);

            return;
        }

        router.post(markRead.url(notification.id));
    }

    function markAllAsRead() {
        if (usingMock) {
            return;
        }

        router.post(markAllRead.url());
    }

    return { items, unreadCount, handleItemClick, markAllAsRead };
}
