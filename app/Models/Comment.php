<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'user_id',
        'incidence_id',
        'parent_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incidence(): BelongsTo
    {
        return $this->belongsTo(Incidence::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
