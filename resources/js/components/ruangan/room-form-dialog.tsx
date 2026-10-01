import { useForm } from '@inertiajs/react';
import { type FormEventHandler, type ReactNode, useState } from 'react';
import RoomManagementController from '@/actions/App/Http/Controllers/Ruangan/RoomManagementController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

export type RoomRecord = {
    id: number;
    name: string;
    building: string | null;
    capacity: number;
    facilities: string | null;
    is_active: boolean;
};

type Props = {
    trigger: ReactNode;
    room?: RoomRecord;
};

type FormData = {
    name: string;
    building: string;
    capacity: string;
    facilities: string;
};

export function RoomFormDialog({ trigger, room }: Props) {
    const [open, setOpen] = useState(false);
    const isEdit = Boolean(room);

    const { data, setData, post, put, processing, errors, reset } =
        useForm<FormData>({
            name: room?.name ?? '',
            building: room?.building ?? '',
            capacity: room ? String(room.capacity) : '',
            facilities: room?.facilities ?? '',
        });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        const options = {
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
                reset();
            },
        };

        if (isEdit && room) {
            put(RoomManagementController.update.url(room.id), options);
        } else {
            post(RoomManagementController.store.url(), options);
        }
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>{trigger}</DialogTrigger>
            <DialogContent>
                <DialogTitle>
                    {isEdit ? 'Edit Ruangan' : 'Tambah Ruangan'}
                </DialogTitle>
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Nama Ruangan</Label>
                        <Input
                            id="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="building">Gedung</Label>
                        <Input
                            id="building"
                            value={data.building}
                            onChange={(e) =>
                                setData('building', e.target.value)
                            }
                        />
                        <InputError message={errors.building} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="capacity">Kapasitas</Label>
                        <Input
                            id="capacity"
                            type="number"
                            min={1}
                            value={data.capacity}
                            onChange={(e) =>
                                setData('capacity', e.target.value)
                            }
                        />
                        <InputError message={errors.capacity} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="facilities">Fasilitas</Label>
                        <Textarea
                            id="facilities"
                            value={data.facilities}
                            onChange={(e) =>
                                setData('facilities', e.target.value)
                            }
                            placeholder="Misal: Proyektor, AC, Whiteboard"
                        />
                        <InputError message={errors.facilities} />
                    </div>

                    <DialogFooter>
                        <Button type="submit" disabled={processing}>
                            {isEdit ? 'Simpan Perubahan' : 'Tambah Ruangan'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
