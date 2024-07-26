<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function tutCategory()
    {
        return $this->belongsTo(TutCategory::class, 'category');
    }

    protected $casts = [
        'keywords' => 'array',
    ];
}
