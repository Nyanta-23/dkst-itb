<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property int|null $unit_id
 * @property string $name
 * @property string $email
 * @property string|null $nip
 * @property string|null $phone
 * @property bool $is_active
 * @property Carbon|null $last_login_at
 * @property Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Unit|null $unit
 * @property-read Collection<int, UserIdentity> $identities
 * @property-read Collection<int, Program> $programs
 * @property-read Collection<int, Technology> $technologies
 * @property-read Collection<int, Tenant> $tenants
 * @property-read Collection<int, RoomBooking> $roomBookings
 * @property-read Collection<int, Letter> $letters
 * @property-read Collection<int, KnowledgeDocument> $knowledgeDocuments
 * @property-read Collection<int, AiMessage> $aiMessages
 */
#[Fillable(['name', 'email', 'password', 'unit_id', 'nip', 'phone', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the unit this user belongs to.
     *
     * @return BelongsTo<Unit, $this>
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the SSO identities linked to this user.
     *
     * @return HasMany<UserIdentity, $this>
     */
    public function identities(): HasMany
    {
        return $this->hasMany(UserIdentity::class);
    }

    /**
     * Get the programs this user is the PIC of.
     *
     * @return HasMany<Program, $this>
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'pic_id');
    }

    /**
     * Get the technologies this user is the PIC of.
     *
     * @return HasMany<Technology, $this>
     */
    public function technologies(): HasMany
    {
        return $this->hasMany(Technology::class, 'pic_id');
    }

    /**
     * Get the tenant accounts linked to this user.
     *
     * @return HasMany<Tenant, $this>
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Get the room bookings made by this user.
     *
     * @return HasMany<RoomBooking, $this>
     */
    public function roomBookings(): HasMany
    {
        return $this->hasMany(RoomBooking::class);
    }

    /**
     * Get the letters created by this user.
     *
     * @return HasMany<Letter, $this>
     */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'created_by');
    }

    /**
     * Get the knowledge documents uploaded by this user.
     *
     * @return HasMany<KnowledgeDocument, $this>
     */
    public function knowledgeDocuments(): HasMany
    {
        return $this->hasMany(KnowledgeDocument::class, 'uploaded_by');
    }

    /**
     * Get the AI assistant messages sent by this user.
     *
     * @return HasMany<AiMessage, $this>
     */
    public function aiMessages(): HasMany
    {
        return $this->hasMany(AiMessage::class);
    }
}
