<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul: Program & Monev
 * Tujuan: Mencatat program kerja DKST, indikator kinerja (termasuk IKU),
 *         dan realisasi per periode beserta proses verifikasinya.
 * Tabel: programs, program_indicators, indicator_realizations
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete()->comment('Unit pemilik/pelaksana program');
            $table->foreignId('pic_id')->nullable()->constrained('users')->nullOnDelete()->comment('Penanggung jawab (PIC) program');
            $table->string('code')->unique()->comment('Kode unik program');
            $table->string('name')->comment('Nama program');
            $table->text('description')->nullable()->comment('Deskripsi program');
            $table->smallInteger('year')->comment('Tahun pelaksanaan program');
            $table->decimal('budget', 15, 2)->comment('Anggaran program');
            $table->date('start_date')->comment('Tanggal mulai program');
            $table->date('end_date')->nullable()->comment('Tanggal selesai program');
            $table->string('status', 20)->comment('Status program: draft, ongoing, completed');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['year', 'status']);
        });

        Schema::create('program_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete()->comment('Program pemilik indikator ini');
            $table->string('name')->comment('Nama indikator');
            $table->boolean('is_iku')->comment('Apakah indikator ini termasuk Indikator Kinerja Utama (IKU)');
            $table->string('iku_code')->nullable()->comment('Kode IKU, jika indikator ini adalah IKU');
            $table->decimal('target', 12, 2)->comment('Target nilai indikator');
            $table->string('measurement_unit', 30)->comment('Satuan pengukuran indikator, contoh: persen, unit');
            $table->timestamps();
        });

        Schema::create('indicator_realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained('program_indicators')->cascadeOnDelete()->comment('Indikator yang direalisasikan');
            $table->string('period', 10)->comment('Periode realisasi, contoh: 2026-Q1');
            $table->decimal('actual_value', 12, 2)->comment('Nilai realisasi aktual');
            $table->text('notes')->nullable()->comment('Catatan tambahan terkait realisasi');
            $table->string('evidence_path')->nullable()->comment('Path berkas bukti pendukung realisasi');
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete()->comment('User yang melaporkan realisasi ini');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->comment('User yang memverifikasi realisasi ini');
            $table->string('status', 20)->comment('Status realisasi: submitted, verified, rejected');
            $table->timestamp('verified_at')->nullable()->comment('Waktu realisasi diverifikasi');
            $table->text('verification_notes')->nullable()->comment('Alasan penolakan / catatan verifikator');
            $table->timestamps();

            $table->unique(['indicator_id', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_realizations');
        Schema::dropIfExists('program_indicators');
        Schema::dropIfExists('programs');
    }
};
