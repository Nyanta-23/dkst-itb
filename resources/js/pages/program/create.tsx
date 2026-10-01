import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { ProgramForm } from '@/components/program/program-form';
import { Card, CardContent } from '@/components/ui/card';
import { create, index } from '@/routes/program';

type Props = {
    units: { id: number; name: string }[];
    users: { id: number; name: string; unit_id: number | null }[];
};

export default function ProgramCreate({ units, users }: Props) {
    return (
        <>
            <Head title="Tambah Program" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Tambah Program"
                    description="Catat program kerja baru beserta indikator kinerjanya."
                />

                <Card className="border-border/60 shadow-sm">
                    <CardContent className="pt-6">
                        <ProgramForm units={units} users={users} />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ProgramCreate.layout = () => ({
    breadcrumbs: [
        { title: 'Program & Monev', href: index() },
        { title: 'Tambah Program', href: create() },
    ],
});
