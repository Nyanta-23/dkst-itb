<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $direktorat = Unit::updateOrCreate(
            ['code' => 'DIR'],
            ['name' => 'Direktorat Kawasan Sains dan Teknologi', 'type' => 'directorate', 'parent_id' => null]
        );

        $subUnits = [
            ['code' => 'SEK', 'name' => 'Sekretariat Direktorat', 'type' => 'secretariat'],
            ['code' => 'DBK', 'name' => 'Deputi Bisnis dan Kemitraan', 'type' => 'deputy'],
            ['code' => 'DTA', 'name' => 'Deputi Alih Teknologi dan Kekayaan Intelektual', 'type' => 'deputy'],
            ['code' => 'DIA', 'name' => 'Deputi Inkubasi dan Akselerasi Bisnis', 'type' => 'deputy'],
            ['code' => 'DTT', 'name' => 'Subdit Data dan Teknologi Informasi', 'type' => 'sub_directorate'],
            ['code' => 'PME', 'name' => 'Subdit Perencanaan, Monitoring, dan Evaluasi', 'type' => 'sub_directorate'],
            ['code' => 'KEU', 'name' => 'Subdit Keuangan', 'type' => 'sub_directorate'],
            ['code' => 'ASI', 'name' => 'Subdit Aset dan Infrastruktur', 'type' => 'sub_directorate'],
        ];

        foreach ($subUnits as $subUnit) {
            Unit::updateOrCreate(
                ['code' => $subUnit['code']],
                [...$subUnit, 'parent_id' => $direktorat->id]
            );
        }

        Unit::updateOrCreate(
            ['code' => 'EXT'],
            ['name' => 'Mitra Eksternal', 'type' => 'external', 'parent_id' => null]
        );
    }
}
