<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Unit|null $parent
 * @property-read Collection<int, Unit> $children
 * @property-read Collection<int, User> $users
 * @property-read Collection<int, Program> $programs
 * @property-read Collection<int, Asset> $assets
 * @property-read Collection<int, Disposition> $dispositions
 */
#[Fillable(['parent_id', 'code', 'name', 'type'])]
class Unit extends Model
{
    use HasFactory;

    /**
     * Valid values for the `type` column.
     *
     * - directorate: direktorat
     * - secretariat: sekretariat
     * - deputy: deputi
     * - sub_directorate: subdit
     * - external: eksternal
     */
    public const TYPES = ['directorate', 'secretariat', 'deputy', 'sub_directorate', 'external'];

    /**
     * Get the parent unit.
     *
     * @return BelongsTo<Unit, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    /**
     * Get the child units.
     *
     * @return HasMany<Unit, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    /**
     * Get the users belonging to this unit.
     *
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the programs owned by this unit.
     *
     * @return HasMany<Program, $this>
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the assets belonging to this unit.
     *
     * @return HasMany<Asset, $this>
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Get the dispositions addressed to this unit.
     *
     * @return HasMany<Disposition, $this>
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class, 'to_unit_id');
    }
}
