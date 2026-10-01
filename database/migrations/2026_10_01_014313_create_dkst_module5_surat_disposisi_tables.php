<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul: Surat & Disposisi
 * Tujuan: Mencatat surat masuk/keluar DKST beserta alur disposisinya
 *         ke unit atau pejabat tujuan.
 * Tabel: letters, dispositions
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->comment('Jenis surat: incoming, outgoing');
            $table->string('letter_number')->comment('Nomor surat');
            $table->string('agenda_number')->comment('Nomor agenda surat');
            $table->string('subject')->comment('Perihal surat');
            $table->string('sender')->comment('Pengirim surat');
            $table->string('recipient')->comment('Penerima surat');
            $table->date('letter_date')->comment('Tanggal surat');
            $table->date('received_date')->nullable()->comment('Tanggal surat diterima (khusus surat masuk)');
            $table->string('classification', 20)->comment('Sifat surat: regular, important, confidential');
            $table->string('file_path')->comment('Path berkas hasil pemindaian surat');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->comment('User yang mencatat surat ini');
            $table->string('status', 20)->comment('Status surat: new, forwarded, completed');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained('letters')->cascadeOnDelete()->comment('Surat yang didisposisikan');
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete()->comment('User pemberi disposisi');
            $table->foreignId('to_unit_id')->constrained('units')->cascadeOnDelete()->comment('Unit tujuan disposisi');
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('User tujuan disposisi, jika spesifik');
            $table->text('instruction')->comment('Instruksi disposisi');
            $table->date('due_date')->nullable()->comment('Batas waktu penyelesaian disposisi');
            $table->string('status', 20)->comment('Status disposisi: new, read, in_progress, completed');
            $table->timestamp('read_at')->nullable()->comment('Waktu disposisi dibaca oleh tujuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositions');
        Schema::dropIfExists('letters');
    }
};
