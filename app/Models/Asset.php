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
 * @property int|null $unit_id
 * @property string $asset_code
 * @property string $name
 * @property string $category
 * @property string|null $location
 * @property string $condition
 * @property Carbon $acquisition_date
 * @property string $acquisition_value
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Unit|null $unit
 */
#[Fillable(['unit_id', 'asset_code', 'name', 'category', 'location', 'condition', 'acquisition_date', 'acquisition_value', 'status'])]
class Asset extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Valid values for the `condition` column.
     *
     * - good: baik
     * - minor_damage: rusak ringan
     * - major_damage: rusak berat
     */
    public const CONDITIONS = ['good', 'minor_damage', 'major_damage'];

    /**
     * Valid values for the `status` column.
     *
     * - active: masih digunakan
     * - borrowed: sedang dipinjam
     * - disposed: sudah dihapuskan
     */
    public const STATUSES = ['active', 'borrowed', 'disposed'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'acquisition_value' => 'decimal:2',
        ];
    }

    /**
     * Get the unit that owns this asset.
     *
     * @return BelongsTo<Unit, $this>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
