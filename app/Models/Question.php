<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Question extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(QnaTag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(QnaComment::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(QnaVote::class);
    }

    public function sessions(): HasManyThrough
    {
        return $this->hasManyThrough(QnaSession::class, Answer::class);
    }
}
