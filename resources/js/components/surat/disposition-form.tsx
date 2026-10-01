import { useForm } from '@inertiajs/react';
import { type FormEventHandler, useMemo } from 'react';
import DispositionController from '@/actions/App/Http/Controllers/Surat/DispositionController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

type Unit = { id: number; name: string };
type UnitUser = { id: number; name: string; unit_id: number };

type Props = {
    letterId: number;
    units: Unit[];
    usersByUnit: Record<string, UnitUser[]>;
};

type FormData = {
    to_unit_id: string;
    to_user_id: string;
    instruction: string;
    due_date: string;
};

export function DispositionForm({ letterId, units, usersByUnit }: Props) {
    const { data, setData, post, processing, errors, reset } =
        useForm<FormData>({
            to_unit_id: '',
            to_user_id: '',
            instruction: '',
            due_date: '',
        });

    const availableUsers = useMemo(
        () => (data.to_unit_id ? (usersByUnit[data.to_unit_id] ?? []) : []),
        [data.to_unit_id, usersByUnit],
    );

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(DispositionController.store.url(letterId), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <Card className="border-border/60 shadow-sm">
            <CardHeader>
                <CardTitle>Buat Disposisi</CardTitle>
            </CardHeader>
            <CardContent>
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div className="grid gap-2">
                            <Label htmlFor="to_unit_id">Unit Tujuan</Label>
                            <Select
                                value={data.to_unit_id}
                                onValueChange={(value) => {
                                    setData('to_unit_id', value);
                                    setData('to_user_id', '');
                                }}
                            >
                                <SelectTrigger
                                    id="to_unit_id"
                                    className="w-full"
                                >
                                    <SelectValue placeholder="Pilih unit tujuan" />
                                </SelectTrigger>
                                <SelectContent>
                                    {units.map((unit) => (
                                        <SelectItem
                                            key={unit.id}
                                            value={String(unit.id)}
                                        >
                                            {unit.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.to_unit_id} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="to_user_id">
                                User Tujuan (opsional)
                            </Label>
                            <Select
                                value={data.to_user_id}
                                onValueChange={(value) =>
                                    setData(
                                        'to_user_id',
                                        value === 'none' ? '' : value,
                                    )
                                }
                                disabled={!data.to_unit_id}
                            >
                                <SelectTrigger
                                    id="to_user_id"
                                    className="w-full"
                                >
                                    <SelectValue placeholder="Seluruh unit" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="none">
                                        Seluruh unit
                                    </SelectItem>
                                    {availableUsers.map((user) => (
                                        <SelectItem
                                            key={user.id}
                                            value={String(user.id)}
                                        >
                                            {user.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <InputError message={errors.to_user_id} />
                        </div>
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="instruction">Instruksi</Label>
                        <Textarea
                            id="instruction"
                            value={data.instruction}
                            onChange={(e) =>
                                setData('instruction', e.target.value)
                            }
                            placeholder="Instruksi tindak lanjut…"
                        />
                        <InputError message={errors.instruction} />
                    </div>

                    <div className="grid gap-2 sm:w-60">
                        <Label htmlFor="due_date">Batas Waktu (opsional)</Label>
                        <Input
                            id="due_date"
                            type="date"
                            value={data.due_date}
                            onChange={(e) =>
                                setData('due_date', e.target.value)
                            }
                        />
                        <InputError message={errors.due_date} />
                    </div>

                    <Button type="submit" disabled={processing}>
                        Kirim Disposisi
                    </Button>
                </form>
            </CardContent>
        </Card>
    );
}
