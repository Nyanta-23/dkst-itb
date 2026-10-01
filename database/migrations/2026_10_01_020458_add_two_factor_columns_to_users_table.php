<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul: Autentikasi (Fortify)
 * Tujuan: Melengkapi tabel users dengan kolom two-factor authentication yang
 *         dibutuhkan Laravel Fortify (App\Actions\Fortify), namun belum ada
 *         pada migration users dasar starter kit ini.
 * Tabel: users (perubahan kolom saja, bukan tabel baru)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')->nullable()->after('password')->comment('Secret TOTP untuk two-factor authentication');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret')->comment('Kode pemulihan two-factor authentication');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes')->comment('Waktu two-factor authentication dikonfirmasi aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at']);
        });
    }
};
