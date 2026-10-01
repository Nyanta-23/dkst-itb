import { cn } from '@/lib/utils';

type Props = {
    className?: string;
    variant?: 'light' | 'dark';
};

/**
 * Text wordmark used in place of a real logo file. Swap this component for an
 * <img> once an official DKST/ITB logo asset is added under public/images.
 */
export default function DkstWordmark({ className, variant = 'dark' }: Props) {
    const isLight = variant === 'light';

    return (
        <span
            className={cn(
                'inline-flex items-baseline gap-1.5 font-semibold tracking-tight',
                className,
            )}
        >
            <span
                className={
                    isLight ? 'text-white' : 'text-dkst-navy dark:text-white'
                }
            >
                DKST
            </span>
            <span
                className={cn(
                    'group-data-[collapsible=icon]:hidden',
                    isLight
                        ? 'text-dkst-cyan-light'
                        : 'text-dkst-cyan dark:text-dkst-cyan-light',
                )}
            >
                ·
            </span>
            <span
                className={cn(
                    'text-sm font-medium group-data-[collapsible=icon]:hidden',
                    isLight ? 'text-white/70' : 'text-muted-foreground',
                )}
            >
                ITB
            </span>
        </span>
    );
}
