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
 * @property int $program_id
 * @property string $name
 * @property bool $is_iku
 * @property string|null $iku_code
 * @property string $target
 * @property string $measurement_unit
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Program $program
 * @property-read Collection<int, IndicatorRealization> $realizations
 */
#[Fillable(['program_id', 'name', 'is_iku', 'iku_code', 'target', 'measurement_unit'])]
class ProgramIndicator extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_iku' => 'boolean',
            'target' => 'decimal:2',
        ];
    }

    /**
     * Get the program this indicator belongs to.
     *
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the realizations reported for this indicator.
     *
     * @return HasMany<IndicatorRealization, $this>
     */
    public function realizations(): HasMany
    {
        return $this->hasMany(IndicatorRealization::class, 'indicator_id');
    }
}
