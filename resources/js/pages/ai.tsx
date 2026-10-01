import { Head } from '@inertiajs/react';
import { AiChat } from '@/components/ai/ai-chat';

export default function Ai() {
    return (
        <>
            <Head title="AI Assistant" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex min-h-0 flex-1 flex-col overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <AiChat />
                </div>
            </div>
        </>
    );
}

Ai.layout = () => ({
    breadcrumbs: [{ title: 'AI Assistant', href: '#' }],
});
