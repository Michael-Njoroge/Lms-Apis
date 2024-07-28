<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function course()
    {
        return $this->hasMany(Course::class);
    }
}
