<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $category
 * @property string|null $file_path
 * @property string $content
 * @property int $uploaded_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $uploader
 */
#[Fillable(['title', 'category', 'file_path', 'content', 'uploaded_by'])]
class KnowledgeDocument extends Model
{
    use HasFactory;

    /**
     * Valid values for the `category` column.
     *
     * - sop: standar operasional prosedur
     * - regulation: regulasi
     * - guide: panduan
     */
    public const CATEGORIES = ['sop', 'regulation', 'guide'];

    /**
     * Get the user who uploaded this document.
     *
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope a query to search documents by keyword using the full-text index.
     *
     * @param  Builder<KnowledgeDocument>  $query
     * @return Builder<KnowledgeDocument>
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->whereFullText(['title', 'content'], $keyword);
    }
}
