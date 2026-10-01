import { Head, router } from '@inertiajs/react';
import { Pencil, Plus, Power, Trash2 } from 'lucide-react';
import RoomManagementController from '@/actions/App/Http/Controllers/Ruangan/RoomManagementController';
import Heading from '@/components/heading';
import {
    RoomFormDialog,
    type RoomRecord,
} from '@/components/ruangan/room-form-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

type Props = {
    rooms: RoomRecord[];
};

export default function RuanganKelola({ rooms }: Props) {
    return (
        <>
            <Head title="Kelola Ruangan" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <Heading
                        title="Kelola Ruangan"
                        description="Tambah, ubah, atau nonaktifkan data ruangan."
                    />
                    <RoomFormDialog
                        trigger={
                            <Button>
                                <Plus />
                                Tambah Ruangan
                            </Button>
                        }
                    />
                </div>

                <Card className="overflow-hidden border-border/60 p-0 shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b bg-muted/40 text-left text-xs font-medium text-muted-foreground uppercase">
                                <tr>
                                    <th className="px-4 py-3">Nama</th>
                                    <th className="px-4 py-3">Gedung</th>
                                    <th className="px-4 py-3">Kapasitas</th>
                                    <th className="px-4 py-3">Fasilitas</th>
                                    <th className="px-4 py-3">Status</th>
                                    <th className="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {rooms.map((room) => (
                                    <tr key={room.id}>
                                        <td className="px-4 py-3 font-medium">
                                            {room.name}
                                        </td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {room.building ?? '—'}
                                        </td>
                                        <td className="px-4 py-3 text-muted-foreground">
                                            {room.capacity} orang
                                        </td>
                                        <td className="max-w-xs truncate px-4 py-3 text-muted-foreground">
                                            {room.facilities ?? '—'}
                                        </td>
                                        <td className="px-4 py-3">
                                            <Badge
                                                variant={
                                                    room.is_active
                                                        ? 'secondary'
                                                        : 'outline'
                                                }
                                            >
                                                {room.is_active
                                                    ? 'Aktif'
                                                    : 'Nonaktif'}
                                            </Badge>
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex gap-1">
                                                <RoomFormDialog
                                                    room={room}
                                                    trigger={
                                                        <Button
                                                            size="icon"
                                                            variant="ghost"
                                                            aria-label="Edit"
                                                        >
                                                            <Pencil />
                                                        </Button>
                                                    }
                                                />
                                                <Button
                                                    size="icon"
                                                    variant="ghost"
                                                    aria-label={
                                                        room.is_active
                                                            ? 'Nonaktifkan'
                                                            : 'Aktifkan'
                                                    }
                                                    onClick={() =>
                                                        router.patch(
                                                            RoomManagementController.toggle.url(
                                                                room.id,
                                                            ),
                                                            {},
                                                            { preserveScroll: true },
                                                        )
                                                    }
                                                >
                                                    <Power />
                                                </Button>
                                                <Dialog>
                                                    <DialogTrigger asChild>
                                                        <Button
                                                            size="icon"
                                                            variant="ghost"
                                                            aria-label="Hapus"
                                                        >
                                                            <Trash2 />
                                                        </Button>
                                                    </DialogTrigger>
                                                    <DialogContent>
                                                        <DialogTitle>
                                                            Hapus ruangan ini?
                                                        </DialogTitle>
                                                        <DialogDescription>
                                                            Ruangan "
                                                            {room.name}" beserta
                                                            seluruh riwayat
                                                            bookingnya akan
                                                            dihapus. Tindakan
                                                            ini tidak dapat
                                                            dibatalkan.
                                                        </DialogDescription>
                                                        <DialogFooter className="gap-2">
                                                            <DialogClose
                                                                asChild
                                                            >
                                                                <Button variant="secondary">
                                                                    Batal
                                                                </Button>
                                                            </DialogClose>
                                                            <Button
                                                                variant="destructive"
                                                                onClick={() =>
                                                                    router.delete(
                                                                        RoomManagementController.destroy.url(
                                                                            room.id,
                                                                        ),
                                                                    )
                                                                }
                                                            >
                                                                Ya, Hapus
                                                            </Button>
                                                        </DialogFooter>
                                                    </DialogContent>
                                                </Dialog>
                                            </div>
                                        </td>
                                    </tr>
                                ))}

                                {rooms.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={6}
                                            className="px-4 py-10 text-center text-muted-foreground"
                                        >
                                            Belum ada ruangan.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </div>
        </>
    );
}

RuanganKelola.layout = () => ({
    breadcrumbs: [{ title: 'Kelola Ruangan', href: '#' }],
});
