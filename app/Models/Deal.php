<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property int|null $technology_id
 * @property int $partner_id
 * @property string $type
 * @property string $value
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Technology|null $technology
 * @property-read Partner $partner
 */
#[Fillable(['technology_id', 'partner_id', 'type', 'value', 'start_date', 'end_date', 'status'])]
class Deal extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Valid values for the `type` column.
     *
     * - license: lisensi
     * - collaboration: kerja sama
     * - spin_off: spin off
     */
    public const TYPES = ['license', 'collaboration', 'spin_off'];

    /**
     * Valid values for the `status` column.
     *
     * - negotiation: sedang negosiasi
     * - active: sedang berjalan
     * - completed: selesai
     * - cancelled: dibatalkan
     */
    public const STATUSES = ['negotiation', 'active', 'completed', 'cancelled'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the technology involved in this deal.
     *
     * @return BelongsTo<Technology, $this>
     */
    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }

    /**
     * Get the partner involved in this deal.
     *
     * @return BelongsTo<Partner, $this>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
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
