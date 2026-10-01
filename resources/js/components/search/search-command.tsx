import { router, usePage } from '@inertiajs/react';
import { Search, type LucideIcon } from 'lucide-react';
import * as LucideIcons from 'lucide-react';
import { useEffect, useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    CommandDialog,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    getAllNavigationItems,
    type NavigationItem,
} from '@/config/navigation';
import { quickActions, type QuickAction } from '@/data/mock/search';
import { toUrl } from '@/lib/utils';

type Props = {
    onOpenAi: () => void;
};

export function SearchCommand({ onOpenAi }: Props) {
    const [open, setOpen] = useState(false);
    const { auth } = usePage().props;
    const isSuperAdmin = auth.roles.includes('super-admin');
    const icons = LucideIcons as unknown as Record<string, LucideIcon>;

    useEffect(() => {
        function handleKeyDown(event: KeyboardEvent) {
            if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
                event.preventDefault();
                setOpen((prev) => !prev);
            }
        }

        document.addEventListener('keydown', handleKeyDown);
        return () => document.removeEventListener('keydown', handleKeyDown);
    }, []);

    const hasPermission = (permission: string) =>
        isSuperAdmin || auth.permissions.includes(permission);

    const menuItems = getAllNavigationItems().filter((item) =>
        hasPermission(item.permission),
    );
    const availableActions = quickActions.filter((action) =>
        hasPermission(action.permission),
    );

    function goToMenu(item: NavigationItem) {
        setOpen(false);
        router.visit(toUrl(item.href));
    }

    function runAction(action: QuickAction) {
        setOpen(false);

        if (action.action === 'open-ai') {
            onOpenAi();
            return;
        }

        if (action.href) {
            router.visit(action.href);
        }
    }

    return (
        <>
            <Button
                variant="outline"
                className="hidden h-9 w-56 items-center justify-start gap-2 text-muted-foreground sm:flex"
                onClick={() => setOpen(true)}
            >
                <Search className="size-4" />
                <span className="flex-1 text-left">Cari...</span>
                <kbd className="pointer-events-none inline-flex items-center gap-1 rounded border bg-muted px-1.5 font-mono text-[10px] font-medium select-none">
                    Ctrl K
                </kbd>
            </Button>
            <Button
                variant="ghost"
                size="icon"
                className="sm:hidden"
                onClick={() => setOpen(true)}
                aria-label="Cari"
            >
                <Search />
            </Button>
            <CommandDialog
                open={open}
                onOpenChange={setOpen}
                title="Pencarian"
                description="Cari menu atau jalankan aksi cepat"
            >
                <CommandInput placeholder="Cari menu atau aksi..." />
                <CommandList>
                    <CommandEmpty>Tidak ada hasil.</CommandEmpty>

                    {menuItems.length > 0 && (
                        <CommandGroup heading="Menu">
                            {menuItems.map((item) => (
                                <CommandItem
                                    key={item.title}
                                    value={item.title}
                                    onSelect={() => goToMenu(item)}
                                >
                                    <item.icon />
                                    {item.title}
                                </CommandItem>
                            ))}
                        </CommandGroup>
                    )}

                    {availableActions.length > 0 && (
                        <CommandGroup heading="Aksi Cepat">
                            {availableActions.map((action) => {
                                const Icon = icons[action.icon] ?? Search;

                                return (
                                    <CommandItem
                                        key={action.id}
                                        value={action.title}
                                        onSelect={() => runAction(action)}
                                    >
                                        <Icon />
                                        {action.title}
                                    </CommandItem>
                                );
                            })}
                        </CommandGroup>
                    )}

                    {/* TODO: tambahkan grup "Data" dari endpoint backend GET /search
                        ketika pencarian isi data (surat, program, teknologi, dll.) tersedia. */}
                </CommandList>
            </CommandDialog>
        </>
    );
}
