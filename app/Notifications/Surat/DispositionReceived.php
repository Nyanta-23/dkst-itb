<?php

namespace App\Notifications\Surat;

use App\Models\Disposition;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Notifikasi database saat user (atau unit) menerima disposisi baru.
 *
 * Bentuk `toDatabase()` sengaja mengikuti `NotificationData` di frontend
 * (title, message, url, icon) agar bisa langsung dipakai lonceng notifikasi.
 */
class DispositionReceived extends Notification
{
    use Queueable;

    public function __construct(private readonly Disposition $disposition) {}

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
        $letter = $this->disposition->letter;

        return [
            'title' => 'Disposisi baru dari '.$this->disposition->fromUser->name,
            'message' => Str::limit("{$letter->subject} — {$this->disposition->instruction}", 120),
            'url' => route('surat.show', $letter),
            'icon' => 'Mail',
        ];
    }
}
