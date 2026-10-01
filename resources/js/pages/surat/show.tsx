import { Head, Link, router } from '@inertiajs/react';
import { Download, Pencil, Trash2 } from 'lucide-react';
import LetterController from '@/actions/App/Http/Controllers/Surat/LetterController';
import Heading from '@/components/heading';
import { DispositionForm } from '@/components/surat/disposition-form';
import {
    DispositionTimeline,
    type DispositionItem,
} from '@/components/surat/disposition-timeline';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { formatStatusLabel } from '@/lib/status-labels';
import { edit, index } from '@/routes/surat';

type LetterDetail = {
    id: number;
    type: 'incoming' | 'outgoing';
    letterNumber: string;
    agendaNumber: string;
    subject: string;
    sender: string;
    recipient: string;
    letterDate: string;
    receivedDate: string | null;
    classification: string;
    status: string;
    fileUrl: string | null;
    creator: string;
    createdAt: string;
};

type Unit = { id: number; name: string };
type UnitUser = { id: number; name: string; unit_id: number };

type Props = {
    letter: LetterDetail;
    dispositions: DispositionItem[];
    units: Unit[];
    usersByUnit: Record<string, UnitUser[]>;
    can: { manage: boolean; dispose: boolean };
};

const CLASSIFICATION_BADGE: Record<
    string,
    'secondary' | 'outline' | 'destructive'
> = {
    regular: 'secondary',
    important: 'outline',
    confidential: 'destructive',
};

export default function LetterShow({
    letter,
    dispositions,
    units,
    usersByUnit,
    can,
}: Props) {
    return (
        <>
            <Head title={letter.subject} />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div className="space-y-2">
                        <Heading
                            title={letter.subject}
                            description={`${letter.letterNumber} · ${formatStatusLabel(letter.type)}`}
                        />
                        <div className="flex flex-wrap gap-2">
                            <Badge
                                variant={
                                    CLASSIFICATION_BADGE[letter.classification]
                                }
                            >
                                {formatStatusLabel(letter.classification)}
                            </Badge>
                            <Badge variant="secondary">
                                {formatStatusLabel(letter.status)}
                            </Badge>
                        </div>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        {letter.fileUrl && (
                            <Button variant="outline" asChild>
                                <a
                                    href={letter.fileUrl}
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <Download />
                                    Unduh PDF
                                </a>
                            </Button>
                        )}

                        {can.manage && (
                            <>
                                <Button variant="outline" asChild>
                                    <Link href={edit(letter.id)}>
                                        <Pencil />
                                        Edit
                                    </Link>
                                </Button>

                                <Dialog>
                                    <DialogTrigger asChild>
                                        <Button variant="destructive">
                                            <Trash2 />
                                            Hapus
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogTitle>
                                            Hapus surat ini?
                                        </DialogTitle>
                                        <DialogDescription>
                                            Surat {letter.letterNumber} beserta
                                            seluruh riwayat disposisinya akan
                                            dihapus. Tindakan ini tidak dapat
                                            dibatalkan.
                                        </DialogDescription>
                                        <DialogFooter className="gap-2">
                                            <DialogClose asChild>
                                                <Button variant="secondary">
                                                    Batal
                                                </Button>
                                            </DialogClose>
                                            <Button
                                                variant="destructive"
                                                onClick={() =>
                                                    router.delete(
                                                        LetterController.destroy.url(
                                                            letter.id,
                                                        ),
                                                    )
                                                }
                                            >
                                                Ya, Hapus
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </>
                        )}
                    </div>
                </div>

                <Card className="border-border/60 shadow-sm">
                    <CardContent className="grid gap-4 pt-6 text-sm sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                Pengirim
                            </p>
                            <p className="font-medium">{letter.sender}</p>
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                Penerima
                            </p>
                            <p className="font-medium">{letter.recipient}</p>
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                Nomor Agenda
                            </p>
                            <p className="font-medium">
                                {letter.agendaNumber}
                            </p>
                        </div>
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                Tanggal Surat
                            </p>
                            <p className="font-medium">{letter.letterDate}</p>
                        </div>
                        {letter.receivedDate && (
                            <div>
                                <p className="text-xs text-muted-foreground uppercase">
                                    Tanggal Diterima
                                </p>
                                <p className="font-medium">
                                    {letter.receivedDate}
                                </p>
                            </div>
                        )}
                        <div>
                            <p className="text-xs text-muted-foreground uppercase">
                                Dicatat Oleh
                            </p>
                            <p className="font-medium">{letter.creator}</p>
                        </div>
                    </CardContent>
                </Card>

                <DispositionTimeline
                    letterId={letter.id}
                    dispositions={dispositions}
                />

                {can.dispose && (
                    <DispositionForm
                        letterId={letter.id}
                        units={units}
                        usersByUnit={usersByUnit}
                    />
                )}
            </div>
        </>
    );
}

LetterShow.layout = (props: Props) => ({
    breadcrumbs: [
        { title: 'Surat & Disposisi', href: index() },
        { title: props.letter.subject, href: '#' },
    ],
});
