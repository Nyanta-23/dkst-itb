<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul: Activity Log
 * Tujuan: Menyediakan tabel log aktivitas dari paket spatie/laravel-activitylog
 *         untuk mencatat perubahan pada model (subject) beserta pelakunya (causer).
 *         Struktur dan nama tabel mengikuti konvensi paket ini (config/activitylog.php).
 * Tabel: activity_log
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'causer');
            $table->json('attribute_changes')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }
};
