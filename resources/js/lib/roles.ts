const ROLE_LABELS: Record<string, string> = {
    'super-admin': 'Super Admin',
    direktur: 'Direktur',
    sekretariat: 'Sekretariat',
    'deputi-transfer-teknologi': 'Deputi Transfer Teknologi',
    'deputi-inkubasi': 'Deputi Inkubasi',
    'deputi-bisnis': 'Deputi Bisnis',
    'subdit-program': 'Subdit Program',
    'subdit-keuangan': 'Subdit Keuangan',
    'subdit-aset': 'Subdit Aset',
    staf: 'Staf',
    eksternal: 'Eksternal',
};

/** Terjemahkan slug role pertama milik user menjadi label bahasa Indonesia. */
export function formatRoleLabel(roles: string[]): string {
    const [role] = roles;

    if (!role) {
        return '';
    }

    return ROLE_LABELS[role] ?? role;
}
