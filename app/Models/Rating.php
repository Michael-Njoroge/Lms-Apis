<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_rating');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
