import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import {
    ProgramForm,
    type ProgramFormValues,
} from '@/components/program/program-form';
import { Card, CardContent } from '@/components/ui/card';
import { edit, index, show } from '@/routes/program';

type Props = {
    program: ProgramFormValues;
    units: { id: number; name: string }[];
    users: { id: number; name: string; unit_id: number | null }[];
};

export default function ProgramEdit({ program, units, users }: Props) {
    return (
        <>
            <Head title={`Edit Program — ${program.name}`} />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Edit Program"
                    description={`Perbarui data program ${program.name}.`}
                />

                <Card className="border-border/60 shadow-sm">
                    <CardContent className="pt-6">
                        <ProgramForm
                            units={units}
                            users={users}
                            program={program}
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ProgramEdit.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Program & Monev', href: index() },
        { title: props.program.name, href: show(props.program.id) },
        { title: 'Edit', href: edit(props.program.id) },
    ],
});
