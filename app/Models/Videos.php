<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Videos extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $casts = [
        'keywords' => 'array',
    ];

    public function videoCategory()
    {
        return $this->belongsTo(VideoCategory::class, 'category');
    }
}
