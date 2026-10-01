import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Card, CardContent } from '@/components/ui/card';
import { LetterForm } from '@/components/surat/letter-form';
import { create, index } from '@/routes/surat';

export default function LetterCreate() {
    return (
        <>
            <Head title="Catat Surat" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Catat Surat"
                    description="Catat surat masuk atau surat keluar baru beserta berkasnya."
                />

                <Card className="border-border/60 shadow-sm">
                    <CardContent className="pt-6">
                        <LetterForm />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

LetterCreate.layout = () => ({
    breadcrumbs: [
        { title: 'Surat & Disposisi', href: index() },
        { title: 'Catat Surat', href: create() },
    ],
});
