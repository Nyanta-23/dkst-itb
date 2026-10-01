<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul: User & Unit
 * Tujuan: Menyimpan struktur unit organisasi DKST (dengan hierarki induk-anak),
 *         memperluas data user bawaan starter kit untuk kebutuhan DKST, dan
 *         menyiapkan tabel identitas SSO ITB (akan kosong selama MVP).
 * Tabel: units, user_identities (serta perubahan pada tabel users)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('units')->nullOnDelete()->comment('ID unit induk (self-reference), null jika unit paling atas');
            $table->string('code', 20)->unique()->comment('Kode unik unit, contoh: DIR, SEK, DBK');
            $table->string('name')->comment('Nama unit');
            $table->string('type', 20)->comment('Jenis unit: directorate, secretariat, deputy, sub_directorate, external');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->nullOnDelete()->comment('Unit tempat user bertugas');
            $table->string('nip', 30)->nullable()->unique()->after('email')->comment('Nomor Induk Pegawai');
            $table->string('phone', 20)->nullable()->after('nip')->comment('Nomor telepon/HP user');
            $table->boolean('is_active')->default(true)->after('phone')->comment('Status aktif akun user');
            $table->timestamp('last_login_at')->nullable()->after('is_active')->comment('Waktu login terakhir');
            $table->string('password')->nullable()->comment('Password login, nullable untuk persiapan SSO')->change();
        });

        Schema::create('user_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('User pemilik identitas ini');
            $table->string('provider', 30)->comment('Nama provider SSO, contoh: itb_sso');
            $table->string('provider_user_id')->comment('ID user pada sisi provider');
            $table->string('provider_email')->nullable()->comment('Email user pada sisi provider');
            $table->json('provider_data')->nullable()->comment('Data mentah tambahan dari provider');
            $table->timestamp('linked_at')->nullable()->comment('Waktu identitas ini ditautkan ke akun');
            $table->timestamp('last_login_at')->nullable()->comment('Waktu login terakhir melalui provider ini');
            $table->timestamps();

            $table->unique(['provider', 'provider_user_id']);
            $table->unique(['user_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_identities');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_id');
            $table->dropColumn(['nip', 'phone', 'is_active', 'last_login_at']);
            $table->string('password')->change();
        });

        Schema::dropIfExists('units');
    }
};
