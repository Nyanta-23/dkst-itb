import { Bar, BarChart, CartesianGrid, XAxis } from 'recharts';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    ChartContainer,
    ChartTooltip,
    ChartTooltipContent,
    type ChartConfig,
} from '@/components/ui/chart';
import { Progress } from '@/components/ui/progress';
import { formatNumber } from '@/lib/format';

export type IkuProgressItem = {
    id: number;
    name: string;
    program: string;
    target: number;
    realized: number;
    measurementUnit: string;
    quarters: { period: string; value: number }[];
};

const chartConfig: ChartConfig = {
    value: {
        label: 'Realisasi',
        color: 'var(--dkst-cyan)',
    },
};

export function IkuProgressCard({ items }: { items: IkuProgressItem[] }) {
    if (items.length === 0) {
        return null;
    }

    return (
        <Card className="border-border/60 shadow-sm">
            <CardHeader>
                <CardTitle>Progres Indikator IKU</CardTitle>
            </CardHeader>
            <CardContent className="grid gap-6 sm:grid-cols-2">
                {items.map((item) => {
                    const percentage = Math.min(
                        100,
                        Math.round((item.realized / item.target) * 100) || 0,
                    );

                    return (
                        <div
                            key={item.id}
                            className="flex flex-col gap-3 rounded-lg border border-border/60 p-4"
                        >
                            <div>
                                <p className="text-sm font-medium">
                                    {item.name}
                                </p>
                                <p className="text-xs text-muted-foreground">
                                    {item.program}
                                </p>
                            </div>

                            <div className="space-y-1.5">
                                <div className="flex items-baseline justify-between text-sm">
                                    <span className="font-semibold">
                                        {formatNumber(item.realized)}
                                        <span className="text-muted-foreground">
                                            {' '}
                                            / {formatNumber(item.target)}{' '}
                                            {item.measurementUnit}
                                        </span>
                                    </span>
                                    <span className="text-xs font-medium text-dkst-navy dark:text-dkst-cyan-light">
                                        {percentage}%
                                    </span>
                                </div>
                                <Progress value={percentage} />
                            </div>

                            {item.quarters.length > 0 && (
                                <ChartContainer
                                    config={chartConfig}
                                    className="h-24 w-full"
                                >
                                    <BarChart data={item.quarters}>
                                        <CartesianGrid vertical={false} />
                                        <XAxis
                                            dataKey="period"
                                            tickLine={false}
                                            axisLine={false}
                                            tickMargin={6}
                                        />
                                        <ChartTooltip
                                            content={<ChartTooltipContent />}
                                        />
                                        <Bar
                                            dataKey="value"
                                            fill="var(--color-value)"
                                            radius={4}
                                        />
                                    </BarChart>
                                </ChartContainer>
                            )}
                        </div>
                    );
                })}
            </CardContent>
        </Card>
    );
}
