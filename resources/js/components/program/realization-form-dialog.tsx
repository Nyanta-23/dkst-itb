import { useForm } from '@inertiajs/react';
import { type FormEventHandler, type ReactNode, useState } from 'react';
import IndicatorRealizationController from '@/actions/App/Http/Controllers/Program/IndicatorRealizationController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Props = {
    trigger: ReactNode;
    programId: number;
    indicatorId: number;
    period: string;
    measurementUnit: string;
    existing?: {
        actualValue: number;
        notes: string | null;
        verificationNotes: string | null;
    };
};

type FormData = {
    period: string;
    actual_value: string;
    notes: string;
    evidence: File | null;
};

export function RealizationFormDialog({
    trigger,
    programId,
    indicatorId,
    period,
    measurementUnit,
    existing,
}: Props) {
    const [open, setOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } =
        useForm<FormData>({
            period,
            actual_value: existing ? String(existing.actualValue) : '',
            notes: existing?.notes ?? '',
            evidence: null,
        });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(
            IndicatorRealizationController.store.url({
                program: programId,
                indicator: indicatorId,
            }),
            {
                preserveScroll: true,
                onSuccess: () => {
                    setOpen(false);
                    reset();
                },
            },
        );
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>{trigger}</DialogTrigger>
            <DialogContent>
                <DialogTitle>Input Realisasi {period}</DialogTitle>
                {existing?.verificationNotes && (
                    <DialogDescription className="text-destructive">
                        Alasan penolakan sebelumnya:{' '}
                        {existing.verificationNotes}
                    </DialogDescription>
                )}
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid gap-2">
                        <Label htmlFor="actual_value">
                            Nilai Realisasi ({measurementUnit})
                        </Label>
                        <Input
                            id="actual_value"
                            type="number"
                            step="0.01"
                            min={0}
                            value={data.actual_value}
                            onChange={(e) =>
                                setData('actual_value', e.target.value)
                            }
                        />
                        <InputError message={errors.actual_value} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="notes">Catatan</Label>
                        <Textarea
                            id="notes"
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                        />
                        <InputError message={errors.notes} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="evidence">
                            Bukti (PDF/gambar, maks 5 MB)
                        </Label>
                        <Input
                            id="evidence"
                            type="file"
                            accept="application/pdf,image/*"
                            onChange={(e) =>
                                setData(
                                    'evidence',
                                    e.target.files?.[0] ?? null,
                                )
                            }
                        />
                        <InputError message={errors.evidence} />
                    </div>

                    <DialogFooter>
                        <Button type="submit" disabled={processing}>
                            Ajukan Realisasi
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
