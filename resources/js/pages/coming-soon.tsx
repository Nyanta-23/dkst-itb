import { Head, Link } from "@inertiajs/react";
import * as LucideIcons from "lucide-react";
import { ArrowLeft, Construction, type LucideIcon } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { dashboard } from "@/routes";

type Props = {
    title: string;
    icon: string;
    description: string;
};

export default function ComingSoon({ title, icon, description }: Props) {
    const icons = LucideIcons as unknown as Record<string, LucideIcon>;
    const Icon = icons[icon] ?? Construction;

    return (
        <>
            <Head title={title} />
            <div className="flex flex-1 items-center justify-center p-4">
                <Card className="flex max-w-md flex-col items-center gap-4 border-border/60 p-10 text-center shadow-sm">
                    <span className="flex size-14 items-center justify-center rounded-xl bg-dkst-navy/10 text-dkst-navy dark:bg-dkst-cyan/10 dark:text-dkst-cyan-light">
                        <Icon className="size-7" />
                    </span>
                    <div className="space-y-1.5">
                        <h1 className="text-xl font-semibold">{title}</h1>
                        <p className="text-sm text-muted-foreground">
                            {description}
                        </p>
                    </div>
                    <p className="text-xs font-medium tracking-wide text-dkst-navy uppercase dark:text-dkst-cyan-light">
                        Modul ini sedang disiapkan
                    </p>
                    <Button variant="outline" asChild>
                        <Link href={dashboard()}>
                            <ArrowLeft />
                            Kembali ke Dashboard
                        </Link>
                    </Button>
                </Card>
            </div>
        </>
    );
}

ComingSoon.layout = (props: Props) => ({
    breadcrumbs: [{ title: props.title, href: "#" }],
});
