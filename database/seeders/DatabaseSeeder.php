<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UnitSeeder::class);
        $this->call(RolePermissionSeeder::class);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Admin DKST',
            'email' => 'admin@dkst.itb.ac.id',
            'unit_id' => Unit::where('code', 'DIR')->value('id'),
        ]);

        // 1. Super Admin
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'ASI')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['super-admin']);

        // 2. Direktur
        $user = User::updateOrCreate(
            ['email' => 'direktur@gmail.com'],
            [
                'name' => 'Direktur DKST',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'DIR')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['direktur']);

        // 3. Sekretariat
        $user = User::updateOrCreate(
            ['email' => 'sekretariat@gmail.com'],
            [
                'name' => 'Staf Sekretariat',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'SEK')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['sekretariat']);

        // 4. Deputi Transfer Teknologi
        $user = User::updateOrCreate(
            ['email' => 'deputi.teknologi@gmail.com'],
            [
                'name' => 'Deputi Transfer Teknologi',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'DTT')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['deputi-transfer-teknologi']);

        // 5. Deputi Inkubasi & Akselerasi Bisnis
        $user = User::updateOrCreate(
            ['email' => 'deputi.inkubasi@gmail.com'],
            [
                'name' => 'Deputi Inkubasi & Akselerasi Bisnis',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'DIA')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['deputi-inkubasi']);

        // 6. Deputi Pengembangan Bisnis Kawasan
        $user = User::updateOrCreate(
            ['email' => 'deputi.bisnis@gmail.com'],
            [
                'name' => 'Deputi Pengembangan Bisnis Kawasan',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'DBK')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['deputi-bisnis']);

        // 7. Subdit Program, Monitoring & Evaluasi
        $user = User::updateOrCreate(
            ['email' => 'program@gmail.com'],
            [
                'name' => 'Staf Program & Monev',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'PME')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['subdit-program']);

        // 8. Subdit Keuangan
        $user = User::updateOrCreate(
            ['email' => 'keuangan@gmail.com'],
            [
                'name' => 'Staf Keuangan',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'KEU')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['subdit-keuangan']);

        // 9. Subdit Pengelolaan Aset & Sistem Informasi
        $user = User::updateOrCreate(
            ['email' => 'aset@gmail.com'],
            [
                'name' => 'Staf Aset & SI',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'ASI')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['subdit-aset']);

        // 10. Staf (unit Deputi Transfer Teknologi)
        $user = User::updateOrCreate(
            ['email' => 'staf@gmail.com'],
            [
                'name' => 'Staf DKST',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'DTT')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['staf']);

        // 11. Eksternal (mitra/startup)
        $user = User::updateOrCreate(
            ['email' => 'mitra@gmail.com'],
            [
                'name' => 'Mitra Industri',
                'password' => Hash::make('password'),
                'unit_id' => Unit::where('code', 'EXT')->value('id'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $user->syncRoles(['eksternal']);

        $this->call(UserSeeder::class);
        $this->call(DemoDataSeeder::class);
    }
}
