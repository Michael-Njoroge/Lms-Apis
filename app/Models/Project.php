<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $casts = [
        'keywords' => 'array',
        'images' => 'array',
        'tech_stack' => 'array',
        'links' => 'array',
    ];

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }
}
