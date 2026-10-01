const STATUS_LABELS: Record<string, string> = {
    // disposisi / surat
    new: 'Baru',
    read: 'Dibaca',
    in_progress: 'Diproses',
    forwarded: 'Didisposisikan',
    completed: 'Selesai',
    // sifat surat
    regular: 'Biasa',
    important: 'Penting',
    confidential: 'Rahasia',
    // jenis surat
    incoming: 'Surat Masuk',
    outgoing: 'Surat Keluar',
    // pemesanan ruangan
    pending: 'Menunggu',
    approved: 'Disetujui',
    rejected: 'Ditolak',
    cancelled: 'Dibatalkan',
    // tahap inkubasi
    pre_incubation: 'Pra-Inkubasi',
    incubation: 'Inkubasi',
    acceleration: 'Akselerasi',
    // status tenant
    active: 'Aktif',
    graduated: 'Lulus',
    withdrawn: 'Keluar',
};

/** Terjemahkan nilai status mentah (snake_case) menjadi label bahasa Indonesia. */
export function formatStatusLabel(status: string): string {
    return STATUS_LABELS[status] ?? status;
}
