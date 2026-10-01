<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $conversation_id
 * @property string $role
 * @property string $content
 * @property array<int, mixed>|null $sources
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
#[Fillable(['user_id', 'conversation_id', 'role', 'content', 'sources'])]
class AiMessage extends Model
{
    use HasFactory;

    /**
     * Valid values for the `role` column.
     *
     * - user: pesan dari pengguna
     * - assistant: pesan balasan dari AI assistant
     */
    public const ROLES = ['user', 'assistant'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sources' => 'array',
        ];
    }

    /**
     * Get the user who sent this message.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
