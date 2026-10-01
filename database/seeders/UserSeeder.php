<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Modul: User & Unit (data demo)
 * Tujuan: Membuat satu user demo per role otorisasi, masing-masing ditempatkan
 *         di unit yang relevan, untuk kebutuhan pengujian hak akses.
 */
class UserSeeder extends Seeder
{
    /**
     * Pemetaan role => [email, nama, kode unit].
     *
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    private const USERS_BY_ROLE = [
        'direktur' => ['direktur@dkst.itb.ac.id', 'Direktur DKST', 'DIR'],
        'sekretariat' => ['sekretariat@dkst.itb.ac.id', 'Staf Sekretariat', 'SEK'],
        'deputi-transfer-teknologi' => ['deputi-transfer-teknologi@dkst.itb.ac.id', 'Deputi Transfer Teknologi', 'DTA'],
        'deputi-inkubasi' => ['deputi-inkubasi@dkst.itb.ac.id', 'Deputi Inkubasi', 'DIA'],
        'deputi-bisnis' => ['deputi-bisnis@dkst.itb.ac.id', 'Deputi Bisnis', 'DBK'],
        'subdit-program' => ['subdit-program@dkst.itb.ac.id', 'Staf Subdit Program', 'PME'],
        'subdit-keuangan' => ['subdit-keuangan@dkst.itb.ac.id', 'Staf Subdit Keuangan', 'KEU'],
        'subdit-aset' => ['subdit-aset@dkst.itb.ac.id', 'Staf Subdit Aset', 'ASI'],
        'staf' => ['staf@dkst.itb.ac.id', 'Staf DKST', 'DTT'],
        'eksternal' => ['mitra@example.com', 'Mitra Eksternal', 'EXT'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::USERS_BY_ROLE as $role => [$email, $name, $unitCode]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'unit_id' => Unit::where('code', $unitCode)->value('id'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$role]);
        }

        // Admin yang sudah dibuat di DatabaseSeeder mendapat role super-admin.
        $admin = User::where('email', 'admin@dkst.itb.ac.id')->first();
        $admin?->syncRoles(['super-admin']);
    }
}
