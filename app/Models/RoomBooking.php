<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property int $room_id
 * @property int $user_id
 * @property Carbon $booking_date
 * @property string $start_time
 * @property string $end_time
 * @property string $purpose
 * @property int $participant_count
 * @property string $status
 * @property int|null $approved_by
 * @property Carbon|null $approved_at
 * @property string|null $approval_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Room $room
 * @property-read User $user
 * @property-read User|null $approver
 */
#[Fillable(['room_id', 'user_id', 'booking_date', 'start_time', 'end_time', 'purpose', 'participant_count', 'status', 'approved_by', 'approved_at', 'approval_notes'])]
class RoomBooking extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Valid values for the `status` column.
     *
     * - pending: menunggu persetujuan
     * - approved: disetujui
     * - rejected: ditolak
     * - cancelled: dibatalkan oleh pemohon
     */
    public const STATUSES = ['pending', 'approved', 'rejected', 'cancelled'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the room being booked.
     *
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user who made this booking.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved this booking.
     *
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Determine whether the given time range conflicts with an existing
     * pending or approved booking for the same room and date.
     */
    public static function hasConflict(int $roomId, string $bookingDate, string $startTime, string $endTime, ?int $ignoreId = null): bool
    {
        return static::query()
            ->where('room_id', $roomId)
            ->where('booking_date', $bookingDate)
            ->whereIn('status', ['pending', 'approved'])
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();
    }

    /**
     * Get the activity log options for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
