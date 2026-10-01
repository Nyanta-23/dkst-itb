<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul: Mitra, Teknologi & KI, Inkubasi
 * Tujuan: Mengelola data mitra kerja sama, teknologi hasil riset beserta aset
 *         kekayaan intelektualnya (KI), transaksi lisensi/kerja sama (deal),
 *         dan tenant yang menjalani proses inkubasi/akselerasi.
 * Tabel: partners, technologies, ip_assets, deals, tenants
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama mitra');
            $table->string('type', 20)->comment('Jenis mitra: industry, startup, government, other');
            $table->string('sector')->nullable()->comment('Bidang/sektor usaha mitra');
            $table->string('address')->nullable()->comment('Alamat mitra');
            $table->string('contact_person')->nullable()->comment('Nama kontak person mitra');
            $table->string('email')->nullable()->comment('Email kontak mitra');
            $table->string('phone', 20)->nullable()->comment('Nomor telepon kontak mitra');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Kode unik teknologi');
            $table->string('title')->comment('Judul teknologi');
            $table->text('description')->nullable()->comment('Deskripsi teknologi');
            $table->string('sector')->nullable()->comment('Bidang teknologi');
            $table->string('faculty')->nullable()->comment('Fakultas asal teknologi');
            $table->text('inventors')->nullable()->comment('Nama-nama inventor, dipisah koma (MVP)');
            $table->tinyInteger('trl')->default(1)->comment('Technology Readiness Level, skala 1-9');
            $table->string('commercialization_status', 20)->comment('Status komersialisasi: research, ready_to_license, licensed, spin_off');
            $table->foreignId('pic_id')->nullable()->constrained('users')->nullOnDelete()->comment('Penanggung jawab (PIC) teknologi');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ip_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technology_id')->constrained('technologies')->cascadeOnDelete()->comment('Teknologi terkait aset KI ini');
            $table->string('type', 20)->comment('Jenis KI: patent, copyright, trademark, industrial_design');
            $table->string('title')->comment('Judul aset KI');
            $table->string('application_number')->nullable()->comment('Nomor permohonan pendaftaran KI');
            $table->string('certificate_number')->nullable()->comment('Nomor sertifikat KI yang terbit');
            $table->date('filing_date')->nullable()->comment('Tanggal pendaftaran KI');
            $table->date('issue_date')->nullable()->comment('Tanggal sertifikat KI terbit');
            $table->string('status', 20)->comment('Status KI: draft, submitted, registered, granted, rejected');
            $table->timestamps();
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technology_id')->nullable()->constrained('technologies')->nullOnDelete()->comment('Teknologi yang menjadi objek transaksi');
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete()->comment('Mitra pihak transaksi');
            $table->string('type', 20)->comment('Jenis transaksi: license, collaboration, spin_off');
            $table->decimal('value', 15, 2)->comment('Nilai transaksi');
            $table->date('start_date')->comment('Tanggal mulai transaksi');
            $table->date('end_date')->nullable()->comment('Tanggal selesai transaksi');
            $table->string('status', 20)->comment('Status transaksi: negotiation, active, completed, cancelled');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete()->comment('Mitra yang menaungi tenant ini');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Akun login tenant');
            $table->string('startup_name')->comment('Nama startup tenant');
            $table->string('founder')->nullable()->comment('Nama founder startup');
            $table->string('sector')->nullable()->comment('Bidang usaha startup');
            $table->string('cohort', 50)->nullable()->comment('Angkatan/cohort inkubasi (teks, MVP)');
            $table->smallInteger('year')->comment('Tahun mengikuti program inkubasi');
            $table->string('stage', 20)->comment('Tahap inkubasi: pre_incubation, incubation, acceleration');
            $table->string('status', 20)->comment('Status tenant: active, graduated, withdrawn');
            $table->text('progress_notes')->nullable()->comment('Catatan perkembangan tenant');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('ip_assets');
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('partners');
    }
};
