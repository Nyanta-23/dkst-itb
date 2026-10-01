import { Bot, Send, Sparkles, User } from 'lucide-react';
import { useEffect, useRef, useState, type KeyboardEvent } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
    getMockAiReply,
    suggestedQuestions,
    type AiChatMessage,
} from '@/data/mock/ai';
import { cn } from '@/lib/utils';

export function AiChat({ className }: { className?: string }) {
    const [messages, setMessages] = useState<AiChatMessage[]>([]);
    const [input, setInput] = useState('');
    const [isTyping, setIsTyping] = useState(false);
    const bottomRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages, isTyping]);

    function sendMessage(content: string) {
        const trimmed = content.trim();

        if (!trimmed || isTyping) {
            return;
        }

        setMessages((prev) => [
            ...prev,
            { id: crypto.randomUUID(), role: 'user', content: trimmed },
        ]);
        setInput('');
        setIsTyping(true);

        // TODO: ganti dengan panggilan endpoint backend POST /ai/chat
        setTimeout(() => {
            const reply = getMockAiReply(trimmed);
            setMessages((prev) => [
                ...prev,
                {
                    id: crypto.randomUUID(),
                    role: 'assistant',
                    content: reply.content,
                    sources: reply.sources,
                },
            ]);
            setIsTyping(false);
        }, 1000);
    }

    function handleKeyDown(event: KeyboardEvent<HTMLTextAreaElement>) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage(input);
        }
    }

    return (
        <div className={cn('flex min-h-0 flex-1 flex-col', className)}>
            <ScrollArea className="flex-1">
                <div className="flex flex-col gap-4 p-4">
                    {messages.length === 0 && (
                        <div className="flex flex-col items-center gap-4 py-8 text-center">
                            <span className="flex size-12 items-center justify-center rounded-full bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                                <Sparkles className="size-6" />
                            </span>
                            <p className="text-sm text-muted-foreground">
                                Tanyakan apa saja tentang program, teknologi,
                                atau layanan DKST.
                            </p>
                            <div className="grid w-full gap-2">
                                {suggestedQuestions.map((question) => (
                                    <Button
                                        key={question}
                                        variant="outline"
                                        size="sm"
                                        className="h-auto justify-start py-2 text-left font-normal whitespace-normal"
                                        onClick={() => sendMessage(question)}
                                    >
                                        {question}
                                    </Button>
                                ))}
                            </div>
                        </div>
                    )}

                    {messages.map((message) => (
                        <div
                            key={message.id}
                            className={cn(
                                'flex gap-3',
                                message.role === 'user' && 'justify-end',
                            )}
                        >
                            {message.role === 'assistant' && (
                                <span className="flex size-8 shrink-0 items-center justify-center rounded-full bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                                    <Bot className="size-4" />
                                </span>
                            )}
                            <div
                                className={cn(
                                    'max-w-[80%] rounded-2xl px-4 py-2.5 text-sm',
                                    message.role === 'user'
                                        ? 'bg-dkst-navy text-white dark:bg-dkst-cyan dark:text-dkst-navy'
                                        : 'bg-muted',
                                )}
                            >
                                <p>{message.content}</p>
                                {message.sources &&
                                    message.sources.length > 0 && (
                                        <div className="mt-2 flex flex-wrap gap-1.5">
                                            {message.sources.map(
                                                (source) => (
                                                    <Badge
                                                        key={source}
                                                        variant="secondary"
                                                        className="text-[11px]"
                                                    >
                                                        Sumber: {source}
                                                    </Badge>
                                                ),
                                            )}
                                        </div>
                                    )}
                            </div>
                            {message.role === 'user' && (
                                <span className="flex size-8 shrink-0 items-center justify-center rounded-full bg-secondary">
                                    <User className="size-4" />
                                </span>
                            )}
                        </div>
                    ))}

                    {isTyping && (
                        <div className="flex gap-3">
                            <span className="flex size-8 shrink-0 items-center justify-center rounded-full bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                                <Bot className="size-4" />
                            </span>
                            <div className="flex items-center gap-1 rounded-2xl bg-muted px-4 py-3">
                                <span className="size-1.5 animate-bounce rounded-full bg-muted-foreground [animation-delay:-0.3s]" />
                                <span className="size-1.5 animate-bounce rounded-full bg-muted-foreground [animation-delay:-0.15s]" />
                                <span className="size-1.5 animate-bounce rounded-full bg-muted-foreground" />
                            </div>
                        </div>
                    )}
                    <div ref={bottomRef} />
                </div>
            </ScrollArea>
            <div className="flex items-end gap-2 border-t p-3">
                <textarea
                    value={input}
                    onChange={(event) => setInput(event.target.value)}
                    onKeyDown={handleKeyDown}
                    rows={1}
                    placeholder="Tulis pertanyaan Anda... (Enter untuk kirim, Shift+Enter baris baru)"
                    className="border-input placeholder:text-muted-foreground focus-visible:ring-ring/50 max-h-32 flex-1 resize-none rounded-md border bg-background px-3 py-2 text-sm outline-none focus-visible:ring-[3px]"
                />
                <Button
                    size="icon"
                    onClick={() => sendMessage(input)}
                    disabled={!input.trim() || isTyping}
                >
                    <Send />
                </Button>
            </div>
        </div>
    );
}
