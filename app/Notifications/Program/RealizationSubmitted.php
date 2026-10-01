<?php

namespace App\Notifications\Program;

use App\Models\IndicatorRealization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi database ke setiap user ber-permission program.verify saat
 * ada realisasi indikator baru yang diajukan.
 */
class RealizationSubmitted extends Notification
{
    use Queueable;

    public function __construct(private readonly IndicatorRealization $realization) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, string>
     */
    public function toDatabase(object $notifiable): array
    {
        $indicator = $this->realization->indicator;

        return [
            'title' => 'Realisasi indikator baru menunggu verifikasi',
            'message' => "{$this->realization->reporter->name} mengajukan realisasi \"{$indicator->name}\" periode {$this->realization->period}.",
            'url' => route('program.verifikasi'),
            'icon' => 'ClipboardList',
        ];
    }
}
