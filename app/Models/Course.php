<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }
    
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'course_lesson');
    }

    public function ratings()
    {
        return $this->belongsToMany(Rating::class, 'course_rating');
    }
}
