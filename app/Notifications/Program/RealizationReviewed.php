<?php

namespace App\Notifications\Program;

use App\Models\IndicatorRealization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi database ke pelapor saat realisasi indikatornya diverifikasi
 * atau ditolak.
 */
class RealizationReviewed extends Notification
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
        $verified = $this->realization->status === 'verified';
        $indicator = $this->realization->indicator;

        $message = $verified
            ? "Realisasi \"{$indicator->name}\" periode {$this->realization->period} telah diverifikasi."
            : "Realisasi \"{$indicator->name}\" periode {$this->realization->period} ditolak: {$this->realization->verification_notes}";

        return [
            'title' => $verified ? 'Realisasi diverifikasi' : 'Realisasi ditolak',
            'message' => $message,
            'url' => route('program.show', $indicator->program_id),
            'icon' => 'ClipboardList',
        ];
    }
}
