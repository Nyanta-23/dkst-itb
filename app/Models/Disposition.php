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
 * @property int $letter_id
 * @property int $from_user_id
 * @property int $to_unit_id
 * @property int|null $to_user_id
 * @property string $instruction
 * @property Carbon|null $due_date
 * @property string $status
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Letter $letter
 * @property-read User $fromUser
 * @property-read Unit $toUnit
 * @property-read User|null $toUser
 */
#[Fillable(['letter_id', 'from_user_id', 'to_unit_id', 'to_user_id', 'instruction', 'due_date', 'status', 'read_at'])]
class Disposition extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Valid values for the `status` column.
     *
     * - new: belum dibaca
     * - read: sudah dibaca
     * - in_progress: sedang diproses
     * - completed: selesai
     */
    public const STATUSES = ['new', 'read', 'in_progress', 'completed'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the letter this disposition belongs to.
     *
     * @return BelongsTo<Letter, $this>
     */
    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class);
    }

    /**
     * Get the user who issued this disposition.
     *
     * @return BelongsTo<User, $this>
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the unit this disposition is addressed to.
     *
     * @return BelongsTo<Unit, $this>
     */
    public function toUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }

    /**
     * Get the user this disposition is addressed to.
     *
     * @return BelongsTo<User, $this>
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Determine whether the given user is the recipient of this disposition
     * (either addressed to them directly, or to their unit at large).
     */
    public function isRecipient(User $user): bool
    {
        if ($this->to_user_id !== null) {
            return $this->to_user_id === $user->id;
        }

        return $this->to_unit_id === $user->unit_id;
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
