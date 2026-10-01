import { useForm } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';
import { type FormEventHandler } from 'react';
import ProgramController from '@/actions/App/Http/Controllers/Program/ProgramController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
type UserOption = { id: number; name: string; unit_id: number | null };

export type IndicatorFormValue = {
    id?: number;
    name: string;
    is_iku: boolean;
    iku_code: string;
    target: string;
    measurement_unit: string;
};

export type ProgramFormValues = {
    id: number;
    unitId: number;
    picId: number | null;
    code: string;
    name: string;
    description: string | null;
    year: number;
    budget: number;
    startDate: string;
    endDate: string | null;
    status: string;
    indicators: {
        id: number;
        name: string;
        isIku: boolean;
        ikuCode: string | null;
        target: number;
        measurementUnit: string;
    }[];
};

type Props = {
    units: Unit[];
    users: UserOption[];
    program?: ProgramFormValues;
};

type FormData = {
    unit_id: string;
    pic_id: string;
    code: string;
    name: string;
    description: string;
    year: string;
    budget: string;
    start_date: string;
    end_date: string;
    status: string;
    indicators: IndicatorFormValue[];
};

function emptyIndicator(): IndicatorFormValue {
    return {
        name: '',
        is_iku: false,
        iku_code: '',
        target: '',
        measurement_unit: '',
    };
}

export function ProgramForm({ units, users, program }: Props) {
    const isEdit = Boolean(program);

    const { data, setData, post, put, processing, errors: rawErrors } =
        useForm<FormData>({
            unit_id: program ? String(program.unitId) : '',
            pic_id: program?.picId ? String(program.picId) : '',
            code: program?.code ?? '',
            name: program?.name ?? '',
            description: program?.description ?? '',
            year: program ? String(program.year) : String(new Date().getFullYear()),
            budget: program ? String(program.budget) : '',
            start_date: program?.startDate ?? '',
            end_date: program?.endDate ?? '',
            status: program?.status ?? 'draft',
            indicators: program
                ? program.indicators.map((indicator) => ({
                      id: indicator.id,
                      name: indicator.name,
                      is_iku: indicator.isIku,
                      iku_code: indicator.ikuCode ?? '',
                      target: String(indicator.target),
                      measurement_unit: indicator.measurementUnit,
                  }))
                : [emptyIndicator()],
        });

    const errors = rawErrors as Record<string, string | undefined>;

    function updateIndicator(
        index: number,
        changes: Partial<IndicatorFormValue>,
    ) {
        setData(
            'indicators',
            data.indicators.map((indicator, i) =>
                i === index ? { ...indicator, ...changes } : indicator,
            ),
        );
    }

    function addIndicator() {
        setData('indicators', [...data.indicators, emptyIndicator()]);
    }

    function removeIndicator(index: number) {
        setData(
            'indicators',
            data.indicators.filter((_, i) => i !== index),
        );
    }

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (isEdit && program) {
            put(ProgramController.update.url(program.id));
        } else {
            post(ProgramController.store.url());
        }
    };

    return (
        <form onSubmit={submit} className="space-y-8">
            <div className="grid gap-4 sm:grid-cols-2">
                <div className="grid gap-2">
                    <Label htmlFor="unit_id">Unit</Label>
                    <Select
                        value={data.unit_id}
                        onValueChange={(value) => setData('unit_id', value)}
                    >
                        <SelectTrigger id="unit_id" className="w-full">
                            <SelectValue placeholder="Pilih unit" />
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
                    <InputError message={errors.unit_id} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="pic_id">PIC (opsional)</Label>
                    <Select
                        value={data.pic_id || 'none'}
                        onValueChange={(value) =>
                            setData('pic_id', value === 'none' ? '' : value)
                        }
                    >
                        <SelectTrigger id="pic_id" className="w-full">
                            <SelectValue placeholder="Pilih PIC" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="none">Tidak ada</SelectItem>
                            {users.map((user) => (
                                <SelectItem
                                    key={user.id}
                                    value={String(user.id)}
                                >
                                    {user.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <InputError message={errors.pic_id} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="code">Kode Program</Label>
                    <Input
                        id="code"
                        value={data.code}
                        onChange={(e) => setData('code', e.target.value)}
                        placeholder="PRG-2026-001"
                    />
                    <InputError message={errors.code} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="name">Nama Program</Label>
                    <Input
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                    />
                    <InputError message={errors.name} />
                </div>

                <div className="grid gap-2 sm:col-span-2">
                    <Label htmlFor="description">Deskripsi</Label>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) =>
                            setData('description', e.target.value)
                        }
                    />
                    <InputError message={errors.description} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="year">Tahun</Label>
                    <Input
                        id="year"
                        type="number"
                        value={data.year}
                        onChange={(e) => setData('year', e.target.value)}
                    />
                    <InputError message={errors.year} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="budget">Anggaran (Rp)</Label>
                    <Input
                        id="budget"
                        type="number"
                        min={0}
                        value={data.budget}
                        onChange={(e) => setData('budget', e.target.value)}
                    />
                    <InputError message={errors.budget} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="start_date">Tanggal Mulai</Label>
                    <Input
                        id="start_date"
                        type="date"
                        value={data.start_date}
                        onChange={(e) =>
                            setData('start_date', e.target.value)
                        }
                    />
                    <InputError message={errors.start_date} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="end_date">Tanggal Selesai</Label>
                    <Input
                        id="end_date"
                        type="date"
                        value={data.end_date}
                        onChange={(e) => setData('end_date', e.target.value)}
                    />
                    <InputError message={errors.end_date} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="status">Status</Label>
                    <Select
                        value={data.status}
                        onValueChange={(value) => setData('status', value)}
                    >
                        <SelectTrigger id="status" className="w-full">
                            <SelectValue placeholder="Pilih status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="draft">Draft</SelectItem>
                            <SelectItem value="ongoing">Berjalan</SelectItem>
                            <SelectItem value="completed">Selesai</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError message={errors.status} />
                </div>
            </div>

            <div className="space-y-3">
                <div className="flex items-center justify-between">
                    <Label className="text-base">Indikator</Label>
                    <Button
                        type="button"
                        size="sm"
                        variant="outline"
                        onClick={addIndicator}
                    >
                        <Plus />
                        Tambah Indikator
                    </Button>
                </div>
                <InputError message={errors.indicators} />

                <div className="space-y-4">
                    {data.indicators.map((indicator, index) => (
                        <div
                            key={index}
                            className="grid gap-3 rounded-lg border border-border/60 p-4 sm:grid-cols-12"
                        >
                            <div className="grid gap-2 sm:col-span-4">
                                <Label>Nama Indikator</Label>
                                <Input
                                    value={indicator.name}
                                    onChange={(e) =>
                                        updateIndicator(index, {
                                            name: e.target.value,
                                        })
                                    }
                                />
                                <InputError
                                    message={
                                        errors[
                                            `indicators.${index}.name`
                                        ]
                                    }
                                />
                            </div>

                            <div className="grid gap-2 sm:col-span-2">
                                <Label>Target</Label>
                                <Input
                                    type="number"
                                    value={indicator.target}
                                    onChange={(e) =>
                                        updateIndicator(index, {
                                            target: e.target.value,
                                        })
                                    }
                                />
                                <InputError
                                    message={
                                        errors[
                                            `indicators.${index}.target`
                                        ]
                                    }
                                />
                            </div>

                            <div className="grid gap-2 sm:col-span-2">
                                <Label>Satuan</Label>
                                <Input
                                    value={indicator.measurement_unit}
                                    onChange={(e) =>
                                        updateIndicator(index, {
                                            measurement_unit: e.target.value,
                                        })
                                    }
                                    placeholder="persen, unit, dsb"
                                />
                                <InputError
                                    message={
                                        errors[
                                            `indicators.${index}.measurement_unit`
                                        ]
                                    }
                                />
                            </div>

                            <div className="grid gap-2 sm:col-span-3">
                                <Label>Kode IKU</Label>
                                <Input
                                    value={indicator.iku_code}
                                    disabled={!indicator.is_iku}
                                    onChange={(e) =>
                                        updateIndicator(index, {
                                            iku_code: e.target.value,
                                        })
                                    }
                                    placeholder="IKU-1"
                                />
                                <InputError
                                    message={
                                        errors[
                                            `indicators.${index}.iku_code`
                                        ]
                                    }
                                />
                            </div>

                            <div className="flex items-end justify-between gap-2 sm:col-span-1">
                                <Button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    aria-label="Hapus indikator"
                                    onClick={() => removeIndicator(index)}
                                    disabled={data.indicators.length === 1}
                                >
                                    <Trash2 />
                                </Button>
                            </div>

                            <label className="flex items-center gap-2 text-sm sm:col-span-12">
                                <Checkbox
                                    checked={indicator.is_iku}
                                    onCheckedChange={(checked) =>
                                        updateIndicator(index, {
                                            is_iku: checked === true,
                                        })
                                    }
                                />
                                Indikator Kinerja Utama (IKU)
                            </label>
                        </div>
                    ))}
                </div>
            </div>

            <Button type="submit" disabled={processing}>
                {isEdit ? 'Simpan Perubahan' : 'Tambah Program'}
            </Button>
        </form>
    );
}
