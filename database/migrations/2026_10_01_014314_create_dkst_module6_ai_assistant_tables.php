<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul: AI Assistant
 * Tujuan: Menyimpan basis pengetahuan (dokumen SOP/regulasi/panduan) untuk
 *         pencarian teks, serta riwayat percakapan AI assistant per user.
 * Tabel: knowledge_documents, ai_messages
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('knowledge_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Judul dokumen pengetahuan');
            $table->string('category', 20)->nullable()->comment('Kategori dokumen: sop, regulation, guide');
            $table->string('file_path')->nullable()->comment('Path berkas dokumen asli, jika ada');
            $table->longText('content')->comment('Isi teks dokumen untuk pencarian full-text');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete()->comment('User yang mengunggah dokumen ini');
            $table->timestamps();

            $table->fullText(['title', 'content']);
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('User pemilik percakapan ini');
            $table->uuid('conversation_id')->index()->comment('ID percakapan, mengelompokkan rangkaian pesan');
            $table->string('role', 20)->comment('Peran pengirim pesan: user, assistant');
            $table->longText('content')->comment('Isi pesan');
            $table->json('sources')->nullable()->comment('Sumber referensi jawaban assistant, jika ada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('knowledge_documents');
    }
};
