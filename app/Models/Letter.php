<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property string $type
 * @property string $letter_number
 * @property string $agenda_number
 * @property string $subject
 * @property string $sender
 * @property string $recipient
 * @property Carbon $letter_date
 * @property Carbon|null $received_date
 * @property string $classification
 * @property string $file_path
 * @property int $created_by
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $creator
 * @property-read Collection<int, Disposition> $dispositions
 */
#[Fillable(['type', 'letter_number', 'agenda_number', 'subject', 'sender', 'recipient', 'letter_date', 'received_date', 'classification', 'file_path', 'created_by', 'status'])]
class Letter extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Valid values for the `type` column.
     *
     * - incoming: surat masuk
     * - outgoing: surat keluar
     */
    public const TYPES = ['incoming', 'outgoing'];

    /**
     * Valid values for the `classification` column.
     *
     * - regular: biasa
     * - important: penting
     * - confidential: rahasia
     */
    public const CLASSIFICATIONS = ['regular', 'important', 'confidential'];

    /**
     * Valid values for the `status` column.
     *
     * - new: baru dicatat
     * - forwarded: sudah didisposisikan
     * - completed: selesai
     */
    public const STATUSES = ['new', 'forwarded', 'completed'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'received_date' => 'date',
        ];
    }

    /**
     * Get the user who recorded this letter.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the dispositions issued for this letter.
     *
     * @return HasMany<Disposition, $this>
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class);
    }

    /**
     * Scope a query to letters visible to the given user.
     *
     * Sekretariat (surat.manage), direktur, dan super-admin melihat seluruh surat
     * termasuk yang bersifat confidential. User lain hanya melihat surat yang
     * didisposisikan ke unit atau dirinya sendiri.
     *
     * @param  Builder<Letter>  $query
     * @return Builder<Letter>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->can('surat.manage') || $user->hasRole(['direktur', 'super-admin'])) {
            return $query;
        }

        return $query->whereHas('dispositions', function (Builder $query) use ($user) {
            $query->where('to_unit_id', $user->unit_id)
                ->orWhere('to_user_id', $user->id);
        });
    }

    /**
     * Sinkronkan status surat berdasarkan status seluruh disposisinya:
     * belum ada disposisi tetap `new`, ada yang belum selesai jadi `forwarded`,
     * semua selesai jadi `completed`.
     */
    public function syncStatusFromDispositions(): void
    {
        $this->loadMissing('dispositions');

        if ($this->dispositions->isEmpty()) {
            return;
        }

        $status = $this->dispositions->every(fn (Disposition $disposition) => $disposition->status === 'completed')
            ? 'completed'
            : 'forwarded';

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
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
