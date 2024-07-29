<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function project()
    {
        return $this->hasMany(Project::class);
    }
}
