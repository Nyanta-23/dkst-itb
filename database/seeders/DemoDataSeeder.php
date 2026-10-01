<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Deal;
use App\Models\Disposition;
use App\Models\IndicatorRealization;
use App\Models\IpAsset;
use App\Models\KnowledgeDocument;
use App\Models\Letter;
use App\Models\Partner;
use App\Models\Program;
use App\Models\ProgramIndicator;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\Technology;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Modul: Data Demo
 * Tujuan: Mengisi data contoh yang realistis dan saling terhubung di seluruh
 *         modul DKST (Program & Monev, Mitra/Teknologi/KI/Inkubasi, Aset &
 *         Ruangan, Surat & Disposisi, AI Assistant), untuk kebutuhan demo
 *         dan pengujian fitur. Aman dijalankan berulang (idempotent).
 */
class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all()->keyBy('email');
        $units = Unit::all()->keyBy('code');

        $this->seedProgramsAndIndicators($users, $units);
        $partners = $this->seedPartners();
        $this->seedTechnologiesAndDeals($users, $partners);
        $this->seedTenants($users, $partners);
        $this->seedAssetsAndRooms($users, $units);
        $this->seedLettersAndDispositions($users, $units);
        $this->seedKnowledgeDocuments($users);
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Unit>  $units
     */
    private function seedProgramsAndIndicators($users, $units): void
    {
        $deputiInkubasi = $users->get('deputi.inkubasi@gmail.com');
        $deputiTeknologi = $users->get('deputi.teknologi@gmail.com');
        $deputiBisnis = $users->get('deputi.bisnis@gmail.com');
        $direktur = $users->get('direktur@gmail.com');

        // Catatan: deputi.teknologi@gmail.com ditempatkan di unit DTT (lihat
        // DatabaseSeeder), sehingga program hilirisasi teknologi ikut memakai
        // unit DTT agar deputi tersebut bisa melaporkan realisasinya sendiri.
        $programs = [
            [
                'code' => 'PRG-2026-001',
                'unit_id' => $units->get('DIA')->id,
                'pic_id' => $deputiInkubasi?->id,
                'name' => 'Peningkatan Kapasitas Inkubasi Startup ITB',
                'description' => 'Program pendampingan dan akselerasi startup binaan DKST.',
                'year' => 2026,
                'budget' => '500000000.00',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'status' => 'ongoing',
                'indicators' => [
                    [
                        'name' => 'Jumlah startup lulus inkubasi',
                        'is_iku' => true,
                        'iku_code' => 'IKU-5',
                        'target' => '10.00',
                        'measurement_unit' => 'startup',
                        'realizations' => [
                            '2026-Q1' => ['value' => '2.00', 'status' => 'verified'],
                            '2026-Q2' => ['value' => '3.00', 'status' => 'verified'],
                            '2026-Q3' => ['value' => '2.00', 'status' => 'submitted'],
                        ],
                    ],
                    [
                        'name' => 'Jumlah mentor aktif',
                        'is_iku' => false,
                        'iku_code' => null,
                        'target' => '20.00',
                        'measurement_unit' => 'orang',
                        'realizations' => [
                            '2026-Q1' => ['value' => '6.00', 'status' => 'verified'],
                            '2026-Q2' => ['value' => '7.00', 'status' => 'verified'],
                            '2026-Q3' => [
                                'value' => '4.00',
                                'status' => 'rejected',
                                'notes' => 'Mentor yang dilaporkan belum menandatangani kontrak.',
                                'reason' => 'Jumlah mentor yang dilaporkan tidak sesuai dengan daftar hadir pelatihan.',
                            ],
                        ],
                    ],
                ],
                'reporter' => $deputiInkubasi,
                'verifier' => $direktur,
            ],
            [
                'code' => 'PRG-2026-002',
                'unit_id' => $units->get('DTT')->id,
                'pic_id' => $deputiTeknologi?->id,
                'name' => 'Hilirisasi Teknologi dan Lisensi',
                'description' => 'Mendorong lisensi dan komersialisasi teknologi hasil riset ITB.',
                'year' => 2026,
                'budget' => '750000000.00',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'status' => 'ongoing',
                'indicators' => [
                    [
                        'name' => 'Jumlah lisensi teknologi baru',
                        'is_iku' => true,
                        'iku_code' => 'IKU-3',
                        'target' => '5.00',
                        'measurement_unit' => 'lisensi',
                        'realizations' => [
                            '2026-Q1' => ['value' => '1.00', 'status' => 'verified'],
                            '2026-Q2' => ['value' => '1.00', 'status' => 'verified'],
                            // Q3 sengaja dibiarkan kosong untuk skenario demo:
                            // deputi.teknologi@gmail.com input realisasi Q3 secara langsung.
                        ],
                    ],
                ],
                'reporter' => $deputiTeknologi,
                'verifier' => $direktur,
            ],
            [
                'code' => 'PRG-2025-003',
                'unit_id' => $units->get('DBK')->id,
                'pic_id' => $deputiBisnis?->id,
                'name' => 'Pengembangan Kemitraan Industri',
                'description' => 'Membangun kerja sama dengan mitra industri dan pemerintah.',
                'year' => 2025,
                'budget' => '300000000.00',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'status' => 'completed',
                'indicators' => [
                    [
                        'name' => 'Jumlah MoU kemitraan baru',
                        'is_iku' => false,
                        'iku_code' => null,
                        'target' => '8.00',
                        'measurement_unit' => 'MoU',
                        'realizations' => [
                            '2025-Q1' => ['value' => '3.00', 'status' => 'verified'],
                            '2025-Q2' => ['value' => '4.00', 'status' => 'verified'],
                        ],
                    ],
                ],
                'reporter' => $deputiBisnis,
                'verifier' => $direktur,
            ],
        ];

        foreach ($programs as $data) {
            $indicators = $data['indicators'];
            $reporter = $data['reporter'];
            $verifier = $data['verifier'];
            unset($data['indicators'], $data['reporter'], $data['verifier']);

            $program = Program::updateOrCreate(['code' => $data['code']], $data);

            foreach ($indicators as $indicatorData) {
                $realizations = $indicatorData['realizations'];
                unset($indicatorData['realizations']);

                $indicator = ProgramIndicator::updateOrCreate(
                    ['program_id' => $program->id, 'name' => $indicatorData['name']],
                    $indicatorData + ['program_id' => $program->id]
                );

                foreach ($realizations as $period => $realizationData) {
                    $isReviewed = in_array($realizationData['status'], ['verified', 'rejected'], true);

                    IndicatorRealization::updateOrCreate(
                        ['indicator_id' => $indicator->id, 'period' => $period],
                        [
                            'actual_value' => $realizationData['value'],
                            'notes' => $realizationData['notes'] ?? 'Realisasi periode ' . $period,
                            'evidence_path' => null,
                            'reported_by' => $reporter?->id,
                            'verified_by' => $isReviewed ? $verifier?->id : null,
                            'status' => $realizationData['status'],
                            'verified_at' => $isReviewed ? now() : null,
                            'verification_notes' => $realizationData['reason'] ?? null,
                        ]
                    );
                }
            }
        }
    }

    /**
     * @return Collection<string, Partner>
     */
    private function seedPartners()
    {
        $partners = [
            ['name' => 'PT Industri Maju Bersama', 'type' => 'industry', 'sector' => 'Manufaktur', 'email' => 'contact@industrimaju.example.com'],
            ['name' => 'Startup Tech Nusantara', 'type' => 'startup', 'sector' => 'Teknologi Informasi', 'email' => 'hello@technusantara.example.com'],
            ['name' => 'Kementerian Riset dan Teknologi', 'type' => 'government', 'sector' => 'Pemerintahan', 'email' => 'humas@kemenristek.example.go.id'],
            ['name' => 'Koperasi Mitra Kampus', 'type' => 'other', 'sector' => 'Jasa', 'email' => 'admin@mitrakampus.example.com'],
            ['name' => 'PT Energi Hijau Nusantara', 'type' => 'industry', 'sector' => 'Energi', 'email' => 'contact@energihijau.example.com'],
            ['name' => 'Kementerian Pertanian', 'type' => 'government', 'sector' => 'Pertanian', 'email' => 'humas@kementan.example.go.id'],
        ];

        $models = collect();

        foreach ($partners as $data) {
            $models->put($data['name'], Partner::updateOrCreate(['name' => $data['name']], $data));
        }

        return $models;
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Partner>  $partners
     */
    private function seedTechnologiesAndDeals($users, $partners): void
    {
        $pic = $users->get('deputi.teknologi@gmail.com');

        $technologies = [
            [
                'code' => 'TEK-001',
                'title' => 'Sensor IoT Kualitas Udara',
                'sector' => 'Elektronika',
                'faculty' => 'STEI',
                'inventors' => 'Dr. Andi Wijaya, Dr. Siti Rahma',
                'trl' => 7,
                'commercialization_status' => 'ready_to_license',
                'pic_id' => $pic?->id,
                'ip_assets' => [
                    ['type' => 'patent', 'title' => 'Paten Sensor IoT Kualitas Udara', 'status' => 'registered', 'application_number' => 'P00202600001'],
                    ['type' => 'trademark', 'title' => 'Merek AirSense', 'status' => 'granted', 'application_number' => 'M00202600010'],
                ],
            ],
            [
                'code' => 'TEK-002',
                'title' => 'Material Komposit Ringan',
                'sector' => 'Material',
                'faculty' => 'FTMD',
                'inventors' => 'Dr. Budi Santoso',
                'trl' => 5,
                'commercialization_status' => 'research',
                'pic_id' => $pic?->id,
                'ip_assets' => [
                    ['type' => 'patent', 'title' => 'Paten Material Komposit Ringan', 'status' => 'submitted', 'application_number' => 'P00202600002'],
                ],
            ],
            [
                'code' => 'TEK-003',
                'title' => 'Platform Analitik Data Pertanian',
                'sector' => 'Teknologi Informasi',
                'faculty' => 'STEI',
                'inventors' => 'Dr. Citra Lestari, Ir. Dewi Kusuma',
                'trl' => 8,
                'commercialization_status' => 'licensed',
                'pic_id' => $pic?->id,
                'ip_assets' => [
                    ['type' => 'copyright', 'title' => 'Hak Cipta Platform Analitik Data Pertanian', 'status' => 'granted', 'application_number' => 'EC00202600003'],
                    ['type' => 'patent', 'title' => 'Paten Algoritma Prediksi Panen', 'status' => 'submitted', 'application_number' => 'P00202600011'],
                ],
                'deal' => ['partner' => 'PT Industri Maju Bersama', 'type' => 'license', 'value' => '250000000.00', 'status' => 'active', 'start_date' => '2026-01-01'],
            ],
            [
                'code' => 'TEK-004',
                'title' => 'Bioplastik dari Limbah Pertanian',
                'sector' => 'Bioteknologi',
                'faculty' => 'SITH',
                'inventors' => 'Dr. Eka Prasetyo',
                'trl' => 6,
                'commercialization_status' => 'spin_off',
                'pic_id' => $pic?->id,
                'ip_assets' => [
                    ['type' => 'patent', 'title' => 'Paten Bioplastik dari Limbah Pertanian', 'status' => 'draft', 'application_number' => null],
                ],
            ],
            [
                'code' => 'TEK-005',
                'title' => 'Baterai Lithium Daur Ulang dari Limbah Elektronik',
                'sector' => 'Energi',
                'faculty' => 'FTI',
                'inventors' => 'Dr. Fajar Nugraha',
                'trl' => 9,
                'commercialization_status' => 'licensed',
                'pic_id' => $pic?->id,
                'ip_assets' => [
                    ['type' => 'patent', 'title' => 'Paten Proses Daur Ulang Baterai Lithium', 'status' => 'granted', 'application_number' => 'P00202600012'],
                ],
                'deal' => ['partner' => 'PT Energi Hijau Nusantara', 'type' => 'license', 'value' => '400000000.00', 'status' => 'active', 'start_date' => '2026-02-01'],
            ],
            [
                'code' => 'TEK-006',
                'title' => 'Alat Diagnostik Cepat Penyakit Tropis',
                'sector' => 'Kesehatan',
                'faculty' => 'SF',
                'inventors' => 'Dr. Gita Permatasari',
                'trl' => 4,
                'commercialization_status' => 'research',
                'pic_id' => $pic?->id,
                'ip_assets' => [
                    ['type' => 'patent', 'title' => 'Paten Alat Diagnostik Cepat Penyakit Tropis', 'status' => 'draft', 'application_number' => null],
                ],
            ],
            [
                'code' => 'TEK-007',
                'title' => 'Sistem Irigasi Presisi Berbasis IoT',
                'sector' => 'Pertanian',
                'faculty' => 'STEI',
                'inventors' => 'Dr. Hendra Kusuma',
                'trl' => 6,
                'commercialization_status' => 'ready_to_license',
                'pic_id' => $pic?->id,
                'ip_assets' => [
                    ['type' => 'patent', 'title' => 'Paten Sistem Irigasi Presisi', 'status' => 'submitted', 'application_number' => 'P00202600013'],
                ],
                'deal' => ['partner' => 'Kementerian Pertanian', 'type' => 'license', 'value' => '150000000.00', 'status' => 'negotiation', 'start_date' => '2026-09-01'],
            ],
            [
                'code' => 'TEK-008',
                'title' => 'Platform Smart City untuk Manajemen Sampah',
                'sector' => 'Smart City',
                'faculty' => 'STEI',
                'inventors' => 'Dr. Indah Wulandari',
                'trl' => 3,
                'commercialization_status' => 'research',
                'pic_id' => $pic?->id,
                'ip_assets' => [
                    ['type' => 'copyright', 'title' => 'Hak Cipta Platform Smart City Sampah', 'status' => 'registered', 'application_number' => 'EC00202600014'],
                ],
                'deal' => ['partner' => 'Kementerian Riset dan Teknologi', 'type' => 'collaboration', 'value' => '100000000.00', 'status' => 'completed', 'start_date' => '2025-06-01', 'end_date' => '2025-12-31'],
            ],
        ];

        foreach ($technologies as $data) {
            $ipAssets = $data['ip_assets'];
            $dealData = $data['deal'] ?? null;
            unset($data['ip_assets'], $data['deal']);

            $technology = Technology::updateOrCreate(['code' => $data['code']], $data);

            foreach ($ipAssets as $ipAssetData) {
                IpAsset::updateOrCreate(
                    ['technology_id' => $technology->id, 'title' => $ipAssetData['title']],
                    $ipAssetData + ['technology_id' => $technology->id]
                );
            }

            if ($dealData) {
                $partner = $partners->get($dealData['partner']);
                unset($dealData['partner']);

                Deal::updateOrCreate(
                    ['technology_id' => $technology->id, 'partner_id' => $partner->id],
                    $dealData + ['technology_id' => $technology->id, 'partner_id' => $partner->id]
                );
            }
        }
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Partner>  $partners
     */
    private function seedTenants($users, $partners): void
    {
        $mitra = $users->get('mitra@example.com');

        $tenants = [
            ['startup_name' => 'Agritech Nusantara', 'partner_id' => null, 'user_id' => null, 'sector' => 'Pertanian & Teknologi', 'cohort' => 'Batch 2026-1', 'year' => 2026, 'stage' => 'incubation', 'status' => 'active'],
            ['startup_name' => 'EcoPack Indonesia', 'partner_id' => $partners->get('Koperasi Mitra Kampus')->id, 'user_id' => null, 'sector' => 'Kemasan Berkelanjutan', 'cohort' => 'Batch 2025-2', 'year' => 2025, 'stage' => 'acceleration', 'status' => 'active'],
            ['startup_name' => 'HealthLink Indonesia', 'partner_id' => $partners->get('Startup Tech Nusantara')->id, 'user_id' => $mitra?->id, 'sector' => 'Kesehatan Digital', 'cohort' => 'Batch 2025-2', 'year' => 2025, 'stage' => 'incubation', 'status' => 'active'],
            ['startup_name' => 'GreenEnergy Kampus', 'partner_id' => null, 'user_id' => null, 'sector' => 'Energi Terbarukan', 'cohort' => 'Batch 2024-1', 'year' => 2024, 'stage' => 'acceleration', 'status' => 'graduated'],
        ];

        foreach ($tenants as $data) {
            Tenant::updateOrCreate(['startup_name' => $data['startup_name']], $data);
        }
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Unit>  $units
     */
    private function seedAssetsAndRooms($users, $units): void
    {
        $assets = [
            ['asset_code' => 'AST-001', 'unit_id' => $units->get('DTT')->id, 'name' => 'Proyektor Epson EB-X500', 'category' => 'Elektronik', 'location' => 'Gudang DTT', 'condition' => 'good', 'acquisition_date' => '2024-03-01', 'acquisition_value' => '8000000.00', 'status' => 'active'],
            ['asset_code' => 'AST-002', 'unit_id' => $units->get('ASI')->id, 'name' => 'Kendaraan Dinas Toyota Avanza', 'category' => 'Kendaraan', 'location' => 'Parkir Direktorat', 'condition' => 'good', 'acquisition_date' => '2023-06-15', 'acquisition_value' => '220000000.00', 'status' => 'active'],
            ['asset_code' => 'AST-003', 'unit_id' => $units->get('DTT')->id, 'name' => 'Server Rack Dell PowerEdge', 'category' => 'Elektronik', 'location' => 'Ruang Server', 'condition' => 'minor_damage', 'acquisition_date' => '2022-11-20', 'acquisition_value' => '95000000.00', 'status' => 'active'],
        ];

        foreach ($assets as $data) {
            Asset::updateOrCreate(['asset_code' => $data['asset_code']], $data);
        }

        $rooms = [
            ['name' => 'Ruang Rapat Direktorat', 'building' => 'Gedung CRCS', 'capacity' => 12, 'facilities' => 'Proyektor, AC, Whiteboard', 'is_active' => true],
            ['name' => 'Ruang Diskusi Inkubasi', 'building' => 'Gedung Inovasi', 'capacity' => 8, 'facilities' => 'TV, Wifi', 'is_active' => true],
            ['name' => 'Aula Seminar', 'building' => 'Gedung CRCS', 'capacity' => 100, 'facilities' => 'Sound system, Proyektor, AC', 'is_active' => true],
        ];

        $roomModels = collect();

        foreach ($rooms as $data) {
            $roomModels->put($data['name'], Room::updateOrCreate(['name' => $data['name']], $data));
        }

        $mitra = $users->get('mitra@gmail.com');
        $staf = $users->get('staf@gmail.com');
        $program = $users->get('program@gmail.com');
        $keuangan = $users->get('keuangan@gmail.com');
        $deputiInkubasi = $users->get('deputi.inkubasi@gmail.com');
        $aset = $users->get('aset@gmail.com');

        $ruangRapat = $roomModels->get('Ruang Rapat Direktorat');
        $ruangDiskusi = $roomModels->get('Ruang Diskusi Inkubasi');
        $aula = $roomModels->get('Aula Seminar');

        $today = today();

        $bookings = [
            // Hari ini & besok: campuran disetujui dan menunggu, agar jadwal harian tidak kosong.
            ['room' => $ruangRapat, 'user' => $staf, 'date' => $today->toDateString(), 'start' => '09:00', 'end' => '10:00', 'purpose' => 'Rapat koordinasi mingguan', 'participants' => 8, 'status' => 'approved'],
            ['room' => $ruangDiskusi, 'user' => $mitra, 'date' => $today->toDateString(), 'start' => '13:00', 'end' => '14:00', 'purpose' => 'Diskusi kerja sama inkubasi', 'participants' => 5, 'status' => 'pending'],
            ['room' => $aula, 'user' => $program, 'date' => $today->copy()->addDay()->toDateString(), 'start' => '10:00', 'end' => '12:00', 'purpose' => 'Sosialisasi program monev', 'participants' => 40, 'status' => 'approved'],
            ['room' => $ruangRapat, 'user' => $keuangan, 'date' => $today->copy()->addDay()->toDateString(), 'start' => '14:00', 'end' => '15:00', 'purpose' => 'Rapat anggaran triwulan', 'participants' => 6, 'status' => 'pending'],
            // Minggu ini.
            ['room' => $ruangDiskusi, 'user' => $deputiInkubasi, 'date' => $today->copy()->addDays(2)->toDateString(), 'start' => '09:00', 'end' => '10:00', 'purpose' => 'Mentoring tenant inkubasi', 'participants' => 4, 'status' => 'approved'],
            // Minggu depan.
            ['room' => $aula, 'user' => $staf, 'date' => $today->copy()->addDays(7)->toDateString(), 'start' => '09:00', 'end' => '11:00', 'purpose' => 'Pelatihan internal staf', 'participants' => 50, 'status' => 'rejected', 'reason' => 'Ruangan sedang dalam perbaikan AC pada tanggal tersebut.'],
            ['room' => $ruangRapat, 'user' => $mitra, 'date' => $today->copy()->addDays(8)->toDateString(), 'start' => '10:00', 'end' => '11:00', 'purpose' => 'Presentasi proposal kemitraan', 'participants' => 5, 'status' => 'cancelled'],
        ];

        foreach ($bookings as $data) {
            $isReviewed = in_array($data['status'], ['approved', 'rejected'], true);

            RoomBooking::updateOrCreate(
                ['room_id' => $data['room']->id, 'user_id' => $data['user']?->id, 'booking_date' => $data['date'], 'start_time' => $data['start']],
                [
                    'room_id' => $data['room']->id,
                    'user_id' => $data['user']?->id,
                    'booking_date' => $data['date'],
                    'start_time' => $data['start'],
                    'end_time' => $data['end'],
                    'purpose' => $data['purpose'],
                    'participant_count' => $data['participants'],
                    'status' => $data['status'],
                    'approved_by' => $isReviewed ? $aset?->id : null,
                    'approved_at' => $isReviewed ? now() : null,
                    'approval_notes' => $data['reason'] ?? null,
                ]
            );
        }
    }

    /**
     * @param  Collection<string, User>  $users
     * @param  Collection<string, Unit>  $units
     */
    private function seedLettersAndDispositions($users, $units): void
    {
        $sekretariat = $users->get('sekretariat@gmail.com');
        $direktur = $users->get('direktur@gmail.com');
        $deputiTeknologi = $users->get('deputi.teknologi@gmail.com');
        $deputiInkubasi = $users->get('deputi.inkubasi@gmail.com');
        $deputiBisnis = $users->get('deputi.bisnis@gmail.com');
        $staf = $users->get('staf@gmail.com');
        $program = $users->get('program@gmail.com');
        $keuangan = $users->get('keuangan@gmail.com');

        $letters = [
            // 1. Masuk, biasa, disposisi berjenjang direktur -> DTT -> staf@gmail.com.
            [
                'letter_number' => '001/DKST/X/2026',
                'data' => [
                    'type' => 'incoming',
                    'agenda_number' => 'AG-2026-045',
                    'subject' => 'Permohonan Kerja Sama Riset',
                    'sender' => 'PT Industri Maju Bersama',
                    'recipient' => 'Direktur DKST ITB',
                    'letter_date' => '2026-09-28',
                    'received_date' => '2026-09-29',
                    'classification' => 'regular',
                    'file_path' => 'letters/demo/001-dkst-x-2026.pdf',
                    'created_by' => $sekretariat?->id,
                ],
                'dispositions' => [
                    [
                        'from_user_id' => $direktur?->id,
                        'to_unit_id' => $units->get('DTT')->id,
                        'to_user_id' => null,
                        'instruction' => 'Mohon ditindaklanjuti dan dikoordinasikan dengan tim terkait.',
                        'due_date' => '2026-10-10',
                        'status' => 'completed',
                    ],
                    [
                        'from_user_id' => $deputiTeknologi?->id,
                        'to_unit_id' => $units->get('DTT')->id,
                        'to_user_id' => $staf?->id,
                        'instruction' => 'Tolong siapkan draf kerja sama dan koordinasikan jadwal pertemuan.',
                        'due_date' => '2026-10-08',
                        'status' => 'in_progress',
                    ],
                ],
            ],
            // 2. Keluar, biasa, belum didisposisikan.
            [
                'letter_number' => '002/DKST/X/2026',
                'data' => [
                    'type' => 'outgoing',
                    'agenda_number' => 'AG-2026-046',
                    'subject' => 'Balasan Permohonan Kerja Sama Riset',
                    'sender' => 'Direktur DKST ITB',
                    'recipient' => 'PT Industri Maju Bersama',
                    'letter_date' => '2026-09-30',
                    'received_date' => null,
                    'classification' => 'regular',
                    'file_path' => 'letters/demo/002-dkst-x-2026.pdf',
                    'created_by' => $sekretariat?->id,
                ],
                'dispositions' => [],
            ],
            // 3. Masuk, penting, disposisi tunggal selesai.
            [
                'letter_number' => '003/DKST/X/2026',
                'data' => [
                    'type' => 'incoming',
                    'agenda_number' => 'AG-2026-047',
                    'subject' => 'Undangan Rapat Koordinasi Kementerian Riset dan Teknologi',
                    'sender' => 'Kementerian Riset dan Teknologi',
                    'recipient' => 'Direktur DKST ITB',
                    'letter_date' => '2026-09-25',
                    'received_date' => '2026-09-26',
                    'classification' => 'important',
                    'file_path' => 'letters/demo/003-dkst-x-2026.pdf',
                    'created_by' => $sekretariat?->id,
                ],
                'dispositions' => [
                    [
                        'from_user_id' => $direktur?->id,
                        'to_unit_id' => $units->get('PME')->id,
                        'to_user_id' => $program?->id,
                        'instruction' => 'Mohon disiapkan bahan paparan untuk rapat koordinasi.',
                        'due_date' => '2026-09-30',
                        'status' => 'completed',
                    ],
                ],
            ],
            // 4. Masuk, rahasia — hanya terlihat oleh sekretariat, direktur, super-admin, dan deputi bisnis.
            [
                'letter_number' => '004/DKST/X/2026',
                'data' => [
                    'type' => 'incoming',
                    'agenda_number' => 'AG-2026-048',
                    'subject' => 'Hasil Evaluasi Kinerja Mitra Kerja Sama',
                    'sender' => 'Inspektorat ITB',
                    'recipient' => 'Direktur DKST ITB',
                    'letter_date' => '2026-09-27',
                    'received_date' => '2026-09-28',
                    'classification' => 'confidential',
                    'file_path' => 'letters/demo/004-dkst-x-2026.pdf',
                    'created_by' => $sekretariat?->id,
                ],
                'dispositions' => [
                    [
                        'from_user_id' => $direktur?->id,
                        'to_unit_id' => $units->get('DBK')->id,
                        'to_user_id' => $deputiBisnis?->id,
                        'instruction' => 'Mohon ditelaah secara rahasia dan laporkan hasilnya langsung ke saya.',
                        'due_date' => '2026-10-05',
                        'status' => 'new',
                    ],
                ],
            ],
            // 5. Keluar, biasa, direview subdit keuangan sebelum dikirim.
            [
                'letter_number' => '005/DKST/X/2026',
                'data' => [
                    'type' => 'outgoing',
                    'agenda_number' => 'AG-2026-049',
                    'subject' => 'Laporan Triwulan Program ke Kementerian Riset dan Teknologi',
                    'sender' => 'Direktur DKST ITB',
                    'recipient' => 'Kementerian Riset dan Teknologi',
                    'letter_date' => '2026-09-29',
                    'received_date' => null,
                    'classification' => 'regular',
                    'file_path' => 'letters/demo/005-dkst-x-2026.pdf',
                    'created_by' => $sekretariat?->id,
                ],
                'dispositions' => [
                    [
                        'from_user_id' => $direktur?->id,
                        'to_unit_id' => $units->get('KEU')->id,
                        'to_user_id' => null,
                        'instruction' => 'Mohon direview anggaran yang tercantum sebelum laporan dikirim.',
                        'due_date' => '2026-10-02',
                        'status' => 'read',
                    ],
                ],
            ],
            // 6. Masuk, penting, disposisi berjenjang antar-deputi.
            [
                'letter_number' => '006/DKST/X/2026',
                'data' => [
                    'type' => 'incoming',
                    'agenda_number' => 'AG-2026-050',
                    'subject' => 'Permohonan Dukungan Inkubasi Startup',
                    'sender' => 'Startup Tech Nusantara',
                    'recipient' => 'Direktur DKST ITB',
                    'letter_date' => '2026-09-24',
                    'received_date' => '2026-09-25',
                    'classification' => 'important',
                    'file_path' => 'letters/demo/006-dkst-x-2026.pdf',
                    'created_by' => $sekretariat?->id,
                ],
                'dispositions' => [
                    [
                        'from_user_id' => $direktur?->id,
                        'to_unit_id' => $units->get('DIA')->id,
                        'to_user_id' => $deputiInkubasi?->id,
                        'instruction' => 'Mohon dikaji kelayakan dukungan inkubasi untuk startup ini.',
                        'due_date' => '2026-10-03',
                        'status' => 'completed',
                    ],
                    [
                        'from_user_id' => $deputiInkubasi?->id,
                        'to_unit_id' => $units->get('DBK')->id,
                        'to_user_id' => $deputiBisnis?->id,
                        'instruction' => 'Mohon koordinasikan potensi kemitraan bisnis untuk startup ini.',
                        'due_date' => '2026-10-06',
                        'status' => 'in_progress',
                    ],
                ],
            ],
        ];

        foreach ($letters as $entry) {
            $dispositions = $entry['dispositions'];
            $status = match (true) {
                $dispositions === [] => 'new',
                collect($dispositions)->every(fn(array $d) => $d['status'] === 'completed') => 'completed',
                default => 'forwarded',
            };

            $letter = Letter::updateOrCreate(
                ['letter_number' => $entry['letter_number']],
                [...$entry['data'], 'letter_number' => $entry['letter_number'], 'status' => $status]
            );

            $this->copyDemoLetterFile($letter->file_path);

            foreach ($dispositions as $data) {
                Disposition::updateOrCreate(
                    ['letter_id' => $letter->id, 'from_user_id' => $data['from_user_id'], 'to_unit_id' => $data['to_unit_id']],
                    [...$data, 'letter_id' => $letter->id, 'read_at' => $data['status'] === 'new' ? null : now()]
                );
            }
        }
    }

    /**
     * Salin berkas PDF contoh ke disk public pada path surat demo, jika belum ada.
     */
    private function copyDemoLetterFile(string $filePath): void
    {
        if (Storage::disk('public')->exists($filePath)) {
            return;
        }

        Storage::disk('public')->put(
            $filePath,
            file_get_contents(database_path('seeders/files/contoh-surat.pdf'))
        );
    }

    /**
     * @param  Collection<string, User>  $users
     */
    private function seedKnowledgeDocuments($users): void
    {
        $deputiTransferTeknologi = $users->get('deputi-transfer-teknologi@dkst.itb.ac.id');
        $subditAset = $users->get('subdit-aset@dkst.itb.ac.id');

        $documents = [
            [
                'title' => 'SOP Lisensi Teknologi',
                'category' => 'sop',
                'content' => 'Prosedur standar pengajuan dan persetujuan lisensi teknologi hasil riset ITB kepada mitra industri.',
                'uploaded_by' => $deputiTransferTeknologi?->id,
            ],
            [
                'title' => 'Panduan Peminjaman Ruangan',
                'category' => 'guide',
                'content' => 'Panduan tata cara pengajuan, persetujuan, dan penggunaan ruangan di lingkungan DKST ITB.',
                'uploaded_by' => $subditAset?->id,
            ],
        ];

        foreach ($documents as $data) {
            KnowledgeDocument::updateOrCreate(['title' => $data['title']], $data);
        }
    }
}
