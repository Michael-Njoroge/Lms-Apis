<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(QnaComment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(QnaVote::class);
    }

    public function qnaSession(): BelongsTo
    {
        return $this->belongsTo(QnaSession::class);
    }
}
