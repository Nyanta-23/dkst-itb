import { Sparkles } from 'lucide-react';
import { AiChat } from '@/components/ai/ai-chat';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

type Props = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
};

export function AiAssistantSheet({ open, onOpenChange }: Props) {
    return (
        <Sheet open={open} onOpenChange={onOpenChange}>
            <Tooltip>
                <TooltipTrigger asChild>
                    <SheetTrigger asChild>
                        <Button
                            variant="ghost"
                            size="icon"
                            aria-label="AI Assistant"
                        >
                            <Sparkles />
                        </Button>
                    </SheetTrigger>
                </TooltipTrigger>
                <TooltipContent>AI Assistant</TooltipContent>
            </Tooltip>
            <SheetContent
                side="right"
                className="w-full gap-0 p-0 sm:max-w-md"
            >
                <SheetHeader className="border-b">
                    <SheetTitle className="flex items-center gap-2">
                        <Sparkles className="size-4 text-dkst-navy dark:text-dkst-cyan-light" />
                        AI Assistant
                    </SheetTitle>
                </SheetHeader>
                <AiChat />
            </SheetContent>
        </Sheet>
    );
}
