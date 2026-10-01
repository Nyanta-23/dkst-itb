<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $partner_id
 * @property int|null $user_id
 * @property string $startup_name
 * @property string|null $founder
 * @property string|null $sector
 * @property string|null $cohort
 * @property int $year
 * @property string $stage
 * @property string $status
 * @property string|null $progress_notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Partner|null $partner
 * @property-read User|null $user
 */
#[Fillable(['partner_id', 'user_id', 'startup_name', 'founder', 'sector', 'cohort', 'year', 'stage', 'status', 'progress_notes'])]
class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Valid values for the `stage` column.
     *
     * - pre_incubation: pra inkubasi
     * - incubation: inkubasi
     * - acceleration: akselerasi
     */
    public const STAGES = ['pre_incubation', 'incubation', 'acceleration'];

    /**
     * Valid values for the `status` column.
     *
     * - active: masih aktif
     * - graduated: lulus program
     * - withdrawn: keluar/mengundurkan diri
     */
    public const STATUSES = ['active', 'graduated', 'withdrawn'];

    /**
     * Get the partner this tenant is associated with.
     *
     * @return BelongsTo<Partner, $this>
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the login account for this tenant.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
