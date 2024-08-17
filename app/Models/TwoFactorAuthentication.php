<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuthentication extends Model {
	use HasFactory, HasUuids;

	protected $table = 'two_factor_authentications';
	protected $guarded = [];
}
