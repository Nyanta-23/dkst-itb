<?php

use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\Mitra\PartnerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Program\IndicatorRealizationController;
use App\Http\Controllers\Program\IndicatorRealizationVerificationController;
use App\Http\Controllers\Program\ProgramController;
use App\Http\Controllers\Ruangan\RoomBookingApprovalController;
use App\Http\Controllers\Ruangan\RoomBookingController;
use App\Http\Controllers\Ruangan\RoomController;
use App\Http\Controllers\Ruangan\RoomManagementController;
use App\Http\Controllers\Surat\DispositionController;
use App\Http\Controllers\Surat\LetterController;
use App\Http\Controllers\Teknologi\DealController;
use App\Http\Controllers\Teknologi\IpAssetController;
use App\Http\Controllers\Teknologi\TechnologyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::get('/', [LetterController::class, 'index'])
            ->middleware('permission:surat.view')
            ->name('index');

        Route::get('create', [LetterController::class, 'create'])
            ->middleware('permission:surat.manage')
            ->name('create');

        Route::post('/', [LetterController::class, 'store'])
            ->middleware('permission:surat.manage')
            ->name('store');

        Route::get('{letter}', [LetterController::class, 'show'])
            ->middleware('permission:surat.view')
            ->name('show');

        Route::get('{letter}/edit', [LetterController::class, 'edit'])
            ->middleware('permission:surat.manage')
            ->name('edit');

        Route::put('{letter}', [LetterController::class, 'update'])
            ->middleware('permission:surat.manage')
            ->name('update');

        Route::delete('{letter}', [LetterController::class, 'destroy'])
            ->middleware('permission:surat.manage')
            ->name('destroy');

        Route::post('{letter}/dispositions', [DispositionController::class, 'store'])
            ->middleware('permission:surat.dispose')
            ->name('dispositions.store');

        Route::patch('{letter}/dispositions/{disposition}', [DispositionController::class, 'update'])
            ->middleware('permission:surat.view')
            ->name('dispositions.update')
            ->scopeBindings();
    });

    Route::get('arsip', ComingSoonController::class)
        ->defaults('title', 'Arsip & Dokumen')
        ->defaults('icon', 'Archive')
        ->defaults('description', 'Kelola arsip dan dokumen kedinasan DKST.')
        ->middleware('permission:surat.view')
        ->name('arsip.index');

    Route::get('rapat', ComingSoonController::class)
        ->defaults('title', 'Rapat & Notulen')
        ->defaults('icon', 'CalendarDays')
        ->defaults('description', 'Kelola jadwal rapat dan notulen hasil rapat.')
        ->middleware('permission:surat.view')
        ->name('rapat.index');

    Route::get('layanan-administrasi', ComingSoonController::class)
        ->defaults('title', 'Layanan Administrasi')
        ->defaults('icon', 'FileCog')
        ->defaults('description', 'Ajukan dan kelola layanan administrasi internal.')
        ->middleware('permission:surat.view')
        ->name('layanan-administrasi.index');

    Route::prefix('program')->name('program.')->group(function () {
        Route::get('/', [ProgramController::class, 'index'])
            ->middleware('permission:program.view')
            ->name('index');

        Route::get('create', [ProgramController::class, 'create'])
            ->middleware('permission:program.manage')
            ->name('create');

        Route::post('/', [ProgramController::class, 'store'])
            ->middleware('permission:program.manage')
            ->name('store');

        Route::get('export-iku', [ProgramController::class, 'exportIku'])
            ->middleware('permission:iku.export')
            ->name('export-iku');

        Route::get('verifikasi', [IndicatorRealizationVerificationController::class, 'index'])
            ->middleware('permission:program.verify')
            ->name('verifikasi');

        Route::patch('verifikasi/{realization}', [IndicatorRealizationVerificationController::class, 'update'])
            ->middleware('permission:program.verify')
            ->name('verifikasi.update');

        Route::get('{program}', [ProgramController::class, 'show'])
            ->middleware('permission:program.view')
            ->name('show');

        Route::get('{program}/edit', [ProgramController::class, 'edit'])
            ->middleware('permission:program.manage')
            ->name('edit');

        Route::put('{program}', [ProgramController::class, 'update'])
            ->middleware('permission:program.manage')
            ->name('update');

        Route::post('{program}/indicators/{indicator}/realisasi', [IndicatorRealizationController::class, 'store'])
            ->middleware('permission:program.report')
            ->name('indicators.realizations.store')
            ->scopeBindings();
    });

    Route::prefix('teknologi')->name('teknologi.')->group(function () {
        Route::get('/', [TechnologyController::class, 'index'])
            ->middleware('permission:teknologi.view')
            ->name('index');

        Route::get('create', [TechnologyController::class, 'create'])
            ->middleware('permission:teknologi.manage')
            ->name('create');

        Route::post('/', [TechnologyController::class, 'store'])
            ->middleware('permission:teknologi.manage')
            ->name('store');

        Route::get('{technology}', [TechnologyController::class, 'show'])
            ->middleware('permission:teknologi.view')
            ->name('show');

        Route::get('{technology}/edit', [TechnologyController::class, 'edit'])
            ->middleware('permission:teknologi.manage')
            ->name('edit');

        Route::put('{technology}', [TechnologyController::class, 'update'])
            ->middleware('permission:teknologi.manage')
            ->name('update');

        Route::post('{technology}/ip-assets', [IpAssetController::class, 'store'])
            ->middleware('permission:teknologi.manage')
            ->name('ip-assets.store');

        Route::put('{technology}/ip-assets/{ipAsset}', [IpAssetController::class, 'update'])
            ->middleware('permission:teknologi.manage')
            ->name('ip-assets.update')
            ->scopeBindings();

        Route::delete('{technology}/ip-assets/{ipAsset}', [IpAssetController::class, 'destroy'])
            ->middleware('permission:teknologi.manage')
            ->name('ip-assets.destroy')
            ->scopeBindings();

        Route::post('{technology}/deals', [DealController::class, 'store'])
            ->middleware('permission:teknologi.manage')
            ->name('deals.store');

        Route::put('{technology}/deals/{deal}', [DealController::class, 'update'])
            ->middleware('permission:teknologi.manage')
            ->name('deals.update')
            ->scopeBindings();
    });

    Route::prefix('mitra')->name('mitra.')->group(function () {
        Route::get('/', [PartnerController::class, 'index'])
            ->middleware('permission:mitra.view')
            ->name('index');

        Route::post('/', [PartnerController::class, 'store'])
            ->middleware('permission:mitra.manage')
            ->name('store');

        Route::get('{partner}', [PartnerController::class, 'show'])
            ->middleware('permission:mitra.view')
            ->name('show');

        Route::put('{partner}', [PartnerController::class, 'update'])
            ->middleware('permission:mitra.manage')
            ->name('update');

        Route::delete('{partner}', [PartnerController::class, 'destroy'])
            ->middleware('permission:mitra.manage')
            ->name('destroy');
    });

    Route::get('inkubasi', ComingSoonController::class)
        ->defaults('title', 'Inkubasi')
        ->defaults('icon', 'Rocket')
        ->defaults('description', 'Pantau progres startup binaan dari inkubasi hingga akselerasi.')
        ->middleware('permission:inkubasi.view')
        ->name('inkubasi.index');

    Route::get('aset', ComingSoonController::class)
        ->defaults('title', 'Aset')
        ->defaults('icon', 'Building2')
        ->defaults('description', 'Kelola inventaris aset milik DKST.')
        ->middleware('permission:aset.view')
        ->name('aset.index');

    Route::prefix('ruangan')->name('ruangan.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])
            ->middleware('permission:ruangan.view')
            ->name('index');

        Route::get('booking/create', [RoomBookingController::class, 'create'])
            ->middleware('permission:ruangan.book')
            ->name('booking.create');

        Route::post('booking', [RoomBookingController::class, 'store'])
            ->middleware('permission:ruangan.book')
            ->name('booking.store');

        Route::patch('booking/{booking}/batalkan', [RoomBookingController::class, 'cancel'])
            ->middleware('permission:ruangan.book')
            ->name('booking.cancel');

        Route::get('booking-saya', [RoomBookingController::class, 'myBookings'])
            ->middleware('permission:ruangan.book')
            ->name('my-bookings');

        Route::get('persetujuan', [RoomBookingApprovalController::class, 'index'])
            ->middleware('permission:ruangan.approve')
            ->name('approvals.index');

        Route::patch('persetujuan/{booking}', [RoomBookingApprovalController::class, 'update'])
            ->middleware('permission:ruangan.approve')
            ->name('approvals.update');

        Route::get('kelola', [RoomManagementController::class, 'index'])
            ->middleware('permission:ruangan.manage')
            ->name('manage.index');

        Route::post('kelola', [RoomManagementController::class, 'store'])
            ->middleware('permission:ruangan.manage')
            ->name('manage.store');

        Route::put('kelola/{room}', [RoomManagementController::class, 'update'])
            ->middleware('permission:ruangan.manage')
            ->name('manage.update');

        Route::patch('kelola/{room}/toggle', [RoomManagementController::class, 'toggle'])
            ->middleware('permission:ruangan.manage')
            ->name('manage.toggle');

        Route::delete('kelola/{room}', [RoomManagementController::class, 'destroy'])
            ->middleware('permission:ruangan.manage')
            ->name('manage.destroy');
    });

    Route::get('ai/knowledge-base', ComingSoonController::class)
        ->defaults('title', 'Knowledge Base')
        ->defaults('icon', 'BookOpen')
        ->defaults('description', 'Kelola dokumen pengetahuan yang digunakan AI Assistant.')
        ->middleware('permission:ai.manage')
        ->name('knowledge-base.index');

    Route::get('users', ComingSoonController::class)
        ->defaults('title', 'Manajemen User')
        ->defaults('icon', 'Users')
        ->defaults('description', 'Kelola akun pengguna, unit, dan peran.')
        ->middleware('permission:admin.manage')
        ->name('users.index');

    Route::get('ai', AiAssistantController::class)
        ->middleware('permission:ai.use')
        ->name('ai.index');

    Route::prefix('notifikasi')->name('notifications.')->middleware('permission:dashboard.view')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('baca-semua', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('{notification}/baca', [NotificationController::class, 'markAsRead'])->name('mark-read');
    });
});
