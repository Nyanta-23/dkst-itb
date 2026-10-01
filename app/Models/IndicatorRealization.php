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
 * @property int $indicator_id
 * @property string $period
 * @property string $actual_value
 * @property string|null $notes
 * @property string|null $evidence_path
 * @property int $reported_by
 * @property int|null $verified_by
 * @property string $status
 * @property Carbon|null $verified_at
 * @property string|null $verification_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ProgramIndicator $indicator
 * @property-read User $reporter
 * @property-read User|null $verifier
 */
#[Fillable(['indicator_id', 'period', 'actual_value', 'notes', 'evidence_path', 'reported_by', 'verified_by', 'status', 'verified_at', 'verification_notes'])]
class IndicatorRealization extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Valid values for the `status` column.
     *
     * - submitted: diajukan, menunggu verifikasi
     * - verified: sudah diverifikasi
     * - rejected: ditolak
     */
    public const STATUSES = ['submitted', 'verified', 'rejected'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'actual_value' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the indicator this realization belongs to.
     *
     * @return BelongsTo<ProgramIndicator, $this>
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(ProgramIndicator::class, 'indicator_id');
    }

    /**
     * Get the user who reported this realization.
     *
     * @return BelongsTo<User, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the user who verified this realization.
     *
     * @return BelongsTo<User, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the current quarterly period string, e.g. "2026-Q4".
     */
    public static function currentPeriod(): string
    {
        return now()->year.'-Q'.(int) ceil(now()->month / 3);
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
