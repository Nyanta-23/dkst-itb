<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
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
 * @property string $code
 * @property string $title
 * @property string|null $description
 * @property string|null $sector
 * @property string|null $faculty
 * @property string|null $inventors
 * @property int $trl
 * @property string $commercialization_status
 * @property int|null $pic_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $pic
 * @property-read Collection<int, IpAsset> $ipAssets
 * @property-read Collection<int, Deal> $deals
 */
#[Fillable(['code', 'title', 'description', 'sector', 'faculty', 'inventors', 'trl', 'commercialization_status', 'pic_id'])]
class Technology extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Valid values for the `commercialization_status` column.
     *
     * - research: masih tahap riset
     * - ready_to_license: siap dilisensikan
     * - licensed: sudah dilisensikan
     * - spin_off: menjadi perusahaan spin off
     */
    public const COMMERCIALIZATION_STATUSES = ['research', 'ready_to_license', 'licensed', 'spin_off'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trl' => 'integer',
        ];
    }

    /**
     * Get the person in charge of this technology.
     *
     * @return BelongsTo<User, $this>
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    /**
     * Get the intellectual property assets for this technology.
     *
     * @return HasMany<IpAsset, $this>
     */
    public function ipAssets(): HasMany
    {
        return $this->hasMany(IpAsset::class);
    }

    /**
     * Get the deals involving this technology.
     *
     * @return HasMany<Deal, $this>
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Apply the commercialization status implied by the given deal, if any:
     * an active license deal makes the technology `licensed`, an active
     * spin_off deal makes it `spin_off`.
     */
    public function applyStatusFromDeal(Deal $deal): void
    {
        if ($deal->status !== 'active') {
            return;
        }

        $status = match ($deal->type) {
            'license' => 'licensed',
            'spin_off' => 'spin_off',
            default => null,
        };

        if ($status && $this->commercialization_status !== $status) {
            $this->update(['commercialization_status' => $status]);
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
