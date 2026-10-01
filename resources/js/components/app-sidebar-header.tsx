import { usePage } from "@inertiajs/react";
import { useState } from "react";
import { AiAssistantSheet } from "@/components/ai/ai-assistant-sheet";
import { Breadcrumbs } from "@/components/breadcrumbs";
import { NotificationBell } from "@/components/notifications/notification-bell";
import { SearchCommand } from "@/components/search/search-command";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { SidebarTrigger } from "@/components/ui/sidebar";
import { UserMenuContent } from "@/components/user-menu-content";
import { useInitials } from "@/hooks/use-initials";
import type { BreadcrumbItem as BreadcrumbItemType } from "@/types";

export function AppSidebarHeader({
    breadcrumbs = [],
}: {
    breadcrumbs?: BreadcrumbItemType[];
}) {
    const { auth } = usePage().props;
    const [aiOpen, setAiOpen] = useState(false);
    const getInitials = useInitials();

    return (
        <header className="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-4 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-6">
            <div className="flex items-center gap-2">
                <SidebarTrigger className="-ml-1" />
                <Breadcrumbs breadcrumbs={breadcrumbs} />
            </div>

            <div className="flex items-center gap-1.5">
                <SearchCommand onOpenAi={() => setAiOpen(true)} />
                <NotificationBell />
                <AiAssistantSheet open={aiOpen} onOpenChange={setAiOpen} />
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button
                            variant="ghost"
                            className="size-9 rounded-full p-1"
                        >
                            <Avatar className="size-7 overflow-hidden rounded-full">
                                <AvatarImage
                                    src={auth.user.avatar}
                                    alt={auth.user.name}
                                />
                                <AvatarFallback className="rounded-full bg-dkst-navy text-xs text-white dark:bg-dkst-cyan dark:text-dkst-navy">
                                    {getInitials(auth.user.name)}
                                </AvatarFallback>
                            </Avatar>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" className="w-56">
                        <UserMenuContent user={auth.user} roles={auth.roles} />
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </header>
    );
}
