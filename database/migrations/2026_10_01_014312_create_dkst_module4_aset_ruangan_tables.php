<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul: Aset & Ruangan
 * Tujuan: Mengelola inventaris aset milik DKST dan pemesanan ruangan
 *         beserta proses persetujuannya.
 * Tabel: assets, rooms, room_bookings
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit pemegang aset');
            $table->string('asset_code')->unique()->comment('Kode unik aset');
            $table->string('name')->comment('Nama aset');
            $table->string('category', 50)->comment('Kategori aset');
            $table->string('location')->nullable()->comment('Lokasi penempatan aset');
            $table->string('condition', 20)->comment('Kondisi aset: good, minor_damage, major_damage');
            $table->date('acquisition_date')->comment('Tanggal perolehan aset');
            $table->decimal('acquisition_value', 15, 2)->comment('Nilai perolehan aset');
            $table->string('status', 20)->comment('Status aset: active, borrowed, disposed');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama ruangan');
            $table->string('building')->nullable()->comment('Nama gedung tempat ruangan berada');
            $table->smallInteger('capacity')->comment('Kapasitas ruangan (jumlah orang)');
            $table->text('facilities')->nullable()->comment('Daftar fasilitas yang tersedia di ruangan');
            $table->boolean('is_active')->default(true)->comment('Status ruangan tersedia untuk dipesan');
            $table->timestamps();
        });

        Schema::create('room_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete()->comment('Ruangan yang dipesan');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('User yang memesan ruangan');
            $table->date('booking_date')->comment('Tanggal pemesanan ruangan');
            $table->time('start_time')->comment('Jam mulai penggunaan ruangan');
            $table->time('end_time')->comment('Jam selesai penggunaan ruangan');
            $table->string('purpose')->comment('Keperluan penggunaan ruangan');
            $table->smallInteger('participant_count')->comment('Jumlah peserta yang akan menggunakan ruangan');
            $table->string('status', 20)->comment('Status pemesanan: pending, approved, rejected');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->comment('User yang menyetujui/menolak pemesanan');
            $table->timestamp('approved_at')->nullable()->comment('Waktu pemesanan disetujui/ditolak');
            $table->text('approval_notes')->nullable()->comment('Catatan dari penyetuju pemesanan');
            $table->timestamps();

            $table->index(['room_id', 'booking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_bookings');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('assets');
    }
};
