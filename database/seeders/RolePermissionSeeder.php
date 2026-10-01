<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Modul: Hak Akses (Role & Permission)
 * Tujuan: Menyiapkan data permission dan role demo untuk otorisasi DKST Platform,
 *         memetakan setiap role internal/eksternal ke kombinasi permission yang relevan.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Modul yang masing-masing punya permission `{module}.view` dan `{module}.manage`.
     *
     * @var array<int, string>
     */
    private const MODULES = ['surat', 'program', 'teknologi', 'mitra', 'inkubasi', 'aset', 'ruangan', 'ai', 'admin'];

    /**
     * Permission tambahan di luar pola `{module}.view` / `{module}.manage`.
     *
     * @var array<int, string>
     */
    private const EXTRA_PERMISSIONS = ['dashboard.view', 'program.verify', 'program.report', 'ruangan.approve', 'ruangan.book', 'ai.use', 'iku.export', 'surat.dispose'];

    /**
     * Permission dasar yang didapat semua role internal (bukan eksternal/super-admin).
     *
     * @var array<int, string>
     */
    private const INTERNAL_BASELINE = ['dashboard.view', 'ai.use', 'ruangan.view', 'ruangan.book'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::MODULES as $module) {
            Permission::firstOrCreate(['name' => "{$module}.view"]);
            Permission::firstOrCreate(['name' => "{$module}.manage"]);
        }

        foreach (self::EXTRA_PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // super-admin tidak diberi permission secara langsung; akses penuh ditangani oleh
        // Gate::before() di AppServiceProvider.
        Role::firstOrCreate(['name' => 'super-admin']);

        $this->syncRole('direktur', [
            ...$this->viewPermissionsExcept(['admin']),
            'program.verify',
            'iku.export',
            'surat.dispose',
        ]);

        $this->syncRole('sekretariat', [
            'surat.view',
            'surat.manage',
        ]);

        $this->syncRole('deputi-transfer-teknologi', [
            'teknologi.view',
            'teknologi.manage',
            'mitra.view',
            'mitra.manage',
            'surat.view',
            'surat.dispose',
            'program.view',
            'program.report',
        ]);

        $this->syncRole('deputi-inkubasi', [
            'inkubasi.view',
            'inkubasi.manage',
            'mitra.view',
            'mitra.manage',
            'surat.view',
            'surat.dispose',
            'program.view',
            'program.report',
        ]);

        $this->syncRole('deputi-bisnis', [
            'mitra.view',
            'mitra.manage',
            'teknologi.view',
            'inkubasi.view',
            'surat.view',
            'surat.dispose',
            'program.view',
            'program.report',
        ]);

        $this->syncRole('subdit-program', [
            'program.view',
            'program.manage',
            'program.verify',
            'iku.export',
            'surat.view',
        ]);

        $this->syncRole('subdit-keuangan', [
            'program.view',
            'surat.view',
        ]);

        $this->syncRole('subdit-aset', [
            'aset.view',
            'aset.manage',
            'ruangan.manage',
            'ruangan.approve',
            'ai.view',
            'ai.manage',
            'surat.view',
        ]);

        $this->syncRole('staf', [
            'surat.view',
            'program.view',
            'program.report',
        ]);

        $this->syncRole('eksternal', [
            'dashboard.view',
            'ruangan.view',
            'ruangan.book',
        ], withBaseline: false);
    }

    /**
     * Ambil nama permission `{module}.view` untuk semua modul kecuali yang dikecualikan.
     *
     * @param  array<int, string>  $except
     * @return array<int, string>
     */
    private function viewPermissionsExcept(array $except): array
    {
        return collect(self::MODULES)
            ->reject(fn (string $module) => in_array($module, $except, true))
            ->map(fn (string $module) => "{$module}.view")
            ->all();
    }

    /**
     * Buat (atau perbarui) role lalu sinkronkan permission-nya, dengan tambahan
     * permission dasar untuk role internal jika diminta.
     *
     * @param  array<int, string>  $permissions
     */
    private function syncRole(string $name, array $permissions, bool $withBaseline = true): void
    {
        $role = Role::firstOrCreate(['name' => $name]);

        $all = $withBaseline ? [...$permissions, ...self::INTERNAL_BASELINE] : $permissions;

        $role->syncPermissions(array_unique($all));
    }
}
