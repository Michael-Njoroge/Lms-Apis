<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VideoCategory extends Model
{
     use HasFactory, HasUuids;
    protected $guarded = [];

    public function video()
    {
        return $this->hasMany(Videos::class);
    }
}
