import { Head } from "@inertiajs/react";
import Heading from "@/components/heading";
import {
    LetterForm,
    type LetterFormValues,
} from "@/components/surat/letter-form";
import { Card, CardContent } from "@/components/ui/card";
import { edit, index, show } from "@/routes/surat";

type Props = {
    letter: LetterFormValues;
};

export default function LetterEdit({ letter }: Props) {
    return (
        <>
            <Head title={`Edit Surat — ${letter.letterNumber}`} />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title="Edit Surat"
                    description={`Perbarui data surat ${letter.letterNumber}.`}
                />

                <Card className="border-border/60 shadow-sm">
                    <CardContent className="pt-6">
                        <LetterForm letter={letter} />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

LetterEdit.layout = (props: Props) => ({
    breadcrumbs: [
        { title: "Surat & Disposisi", href: index() },
        { title: props.letter.subject, href: show(props.letter.id) },
        { title: "Edit", href: edit(props.letter.id) },
    ],
});
