import type { Auth } from '@/types/auth';
import type { SharedNotifications } from '@/types/ui';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            notificationsSummary: SharedNotifications | null;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
