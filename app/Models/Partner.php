<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $sector
 * @property string|null $address
 * @property string|null $contact_person
 * @property string|null $email
 * @property string|null $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Deal> $deals
 * @property-read Collection<int, Tenant> $tenants
 */
#[Fillable(['name', 'type', 'sector', 'address', 'contact_person', 'email', 'phone'])]
class Partner extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Valid values for the `type` column.
     *
     * - industry: industri
     * - startup: startup
     * - government: pemerintah
     * - other: lainnya
     */
    public const TYPES = ['industry', 'startup', 'government', 'other'];

    /**
     * Get the deals made with this partner.
     *
     * @return HasMany<Deal, $this>
     */
    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    /**
     * Get the tenants linked to this partner.
     *
     * @return HasMany<Tenant, $this>
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Determine whether this partner has any deal that is still active.
     */
    public function hasActiveDeal(): bool
    {
        return $this->deals()->where('status', 'active')->exists();
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
