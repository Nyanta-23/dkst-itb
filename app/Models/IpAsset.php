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
 * @property int $technology_id
 * @property string $type
 * @property string $title
 * @property string|null $application_number
 * @property string|null $certificate_number
 * @property Carbon|null $filing_date
 * @property Carbon|null $issue_date
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Technology $technology
 */
#[Fillable(['technology_id', 'type', 'title', 'application_number', 'certificate_number', 'filing_date', 'issue_date', 'status'])]
class IpAsset extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Valid values for the `type` column.
     *
     * - patent: paten
     * - copyright: hak cipta
     * - trademark: merek
     * - industrial_design: desain industri
     */
    public const TYPES = ['patent', 'copyright', 'trademark', 'industrial_design'];

    /**
     * Valid values for the `status` column.
     *
     * - draft: belum diajukan
     * - submitted: sudah diajukan
     * - registered: sudah terdaftar
     * - granted: sudah diberikan/granted
     * - rejected: ditolak
     */
    public const STATUSES = ['draft', 'submitted', 'registered', 'granted', 'rejected'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filing_date' => 'date',
            'issue_date' => 'date',
        ];
    }

    /**
     * Get the technology this IP asset belongs to.
     *
     * @return BelongsTo<Technology, $this>
     */
    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
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
