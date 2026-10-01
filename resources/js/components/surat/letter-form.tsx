import { useForm } from '@inertiajs/react';
import { type FormEventHandler } from 'react';
import LetterController from '@/actions/App/Http/Controllers/Surat/LetterController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

export type LetterFormValues = {
    id: number;
    type: string;
    letterNumber: string;
    agendaNumber: string;
    subject: string;
    sender: string;
    recipient: string;
    letterDate: string;
    receivedDate: string | null;
    classification: string;
    fileUrl: string | null;
};

type Props = {
    letter?: LetterFormValues;
};

type FormData = {
    type: string;
    letter_number: string;
    agenda_number: string;
    subject: string;
    sender: string;
    recipient: string;
    letter_date: string;
    received_date: string;
    classification: string;
    file: File | null;
};

export function LetterForm({ letter }: Props) {
    const isEdit = Boolean(letter);

    const { data, setData, post, put, processing, errors } = useForm<FormData>({
        type: letter?.type ?? 'incoming',
        letter_number: letter?.letterNumber ?? '',
        agenda_number: letter?.agendaNumber ?? '',
        subject: letter?.subject ?? '',
        sender: letter?.sender ?? '',
        recipient: letter?.recipient ?? '',
        letter_date: letter?.letterDate ?? '',
        received_date: letter?.receivedDate ?? '',
        classification: letter?.classification ?? 'regular',
        file: null,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEdit && letter) {
            put(LetterController.update.url(letter.id));
        } else {
            post(LetterController.store.url());
        }
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid gap-4 sm:grid-cols-2">
                <div className="grid gap-2">
                    <Label htmlFor="type">Jenis Surat</Label>
                    <Select
                        value={data.type}
                        onValueChange={(value) => setData('type', value)}
                    >
                        <SelectTrigger id="type" className="w-full">
                            <SelectValue placeholder="Pilih jenis surat" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="incoming">
                                Surat Masuk
                            </SelectItem>
                            <SelectItem value="outgoing">
                                Surat Keluar
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.type} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="classification">Sifat Surat</Label>
                    <Select
                        value={data.classification}
                        onValueChange={(value) =>
                            setData('classification', value)
                        }
                    >
                        <SelectTrigger id="classification" className="w-full">
                            <SelectValue placeholder="Pilih sifat surat" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="regular">Biasa</SelectItem>
                            <SelectItem value="important">Penting</SelectItem>
                            <SelectItem value="confidential">
                                Rahasia
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.classification} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="letter_number">Nomor Surat</Label>
                    <Input
                        id="letter_number"
                        value={data.letter_number}
                        onChange={(e) =>
                            setData('letter_number', e.target.value)
                        }
                        placeholder="001/DKST/X/2026"
                    />
                    <InputError message={errors.letter_number} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="agenda_number">Nomor Agenda</Label>
                    <Input
                        id="agenda_number"
                        value={data.agenda_number}
                        onChange={(e) =>
                            setData('agenda_number', e.target.value)
                        }
                        placeholder="AG-2026-001"
                    />
                    <InputError message={errors.agenda_number} />
                </div>

                <div className="grid gap-2 sm:col-span-2">
                    <Label htmlFor="subject">Perihal</Label>
                    <Input
                        id="subject"
                        value={data.subject}
                        onChange={(e) => setData('subject', e.target.value)}
                        placeholder="Perihal surat"
                    />
                    <InputError message={errors.subject} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="sender">Pengirim</Label>
                    <Input
                        id="sender"
                        value={data.sender}
                        onChange={(e) => setData('sender', e.target.value)}
                        placeholder="Nama pengirim / instansi"
                    />
                    <InputError message={errors.sender} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="recipient">Penerima</Label>
                    <Input
                        id="recipient"
                        value={data.recipient}
                        onChange={(e) => setData('recipient', e.target.value)}
                        placeholder="Nama penerima / jabatan"
                    />
                    <InputError message={errors.recipient} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="letter_date">Tanggal Surat</Label>
                    <Input
                        id="letter_date"
                        type="date"
                        value={data.letter_date}
                        onChange={(e) =>
                            setData('letter_date', e.target.value)
                        }
                    />
                    <InputError message={errors.letter_date} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="received_date">
                        Tanggal Diterima (surat masuk)
                    </Label>
                    <Input
                        id="received_date"
                        type="date"
                        value={data.received_date}
                        onChange={(e) =>
                            setData('received_date', e.target.value)
                        }
                    />
                    <InputError message={errors.received_date} />
                </div>

                <div className="grid gap-2 sm:col-span-2">
                    <Label htmlFor="file">Berkas Surat (PDF, maks 5 MB)</Label>
                    <Input
                        id="file"
                        type="file"
                        accept="application/pdf"
                        onChange={(e) =>
                            setData('file', e.target.files?.[0] ?? null)
                        }
                    />
                    {letter?.fileUrl && (
                        <p className="text-xs text-muted-foreground">
                            Berkas saat ini:{' '}
                            <a
                                href={letter.fileUrl}
                                target="_blank"
                                rel="noreferrer"
                                className="underline"
                            >
                                lihat berkas
                            </a>
                            . Kosongkan jika tidak ingin mengganti.
                        </p>
                    )}
                    <InputError message={errors.file} />
                </div>
            </div>

            <div className="flex items-center gap-4">
                <Button type="submit" disabled={processing}>
                    {isEdit ? 'Simpan Perubahan' : 'Catat Surat'}
                </Button>
            </div>
        </form>
    );
}
