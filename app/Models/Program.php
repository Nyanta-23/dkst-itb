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
 * @property int $unit_id
 * @property int|null $pic_id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $year
 * @property string $budget
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Unit $unit
 * @property-read User|null $pic
 * @property-read Collection<int, ProgramIndicator> $indicators
 */
#[Fillable(['unit_id', 'pic_id', 'code', 'name', 'description', 'year', 'budget', 'start_date', 'end_date', 'status'])]
class Program extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Valid values for the `status` column.
     *
     * - draft: belum berjalan
     * - ongoing: sedang berjalan
     * - completed: selesai
     */
    public const STATUSES = ['draft', 'ongoing', 'completed'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the unit that owns this program.
     *
     * @return BelongsTo<Unit, $this>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the person in charge of this program.
     *
     * @return BelongsTo<User, $this>
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    /**
     * Get the indicators for this program.
     *
     * @return HasMany<ProgramIndicator, $this>
     */
    public function indicators(): HasMany
    {
        return $this->hasMany(ProgramIndicator::class);
    }

    /**
     * Scope a query to programs visible to the given user.
     *
     * Pemegang program.manage/program.verify, direktur, subdit-keuangan, dan
     * super-admin melihat semua program. User lain hanya melihat program milik
     * unitnya sendiri.
     *
     * @param  Builder<Program>  $query
     * @return Builder<Program>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->can('program.manage') || $user->can('program.verify') || $user->hasRole(['direktur', 'subdit-keuangan', 'super-admin'])) {
            return $query;
        }

        return $query->where('unit_id', $user->unit_id);
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
