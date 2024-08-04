<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\PasswordReset;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function createResetPasswordToken()
    {
        $resetToken = Str::random(32);

        PasswordReset::updateOrCreate(
            ['email' => $this->email],
            [
                'token' => Hash::make($resetToken),
                'created_at' => Carbon::now()
            ]
        );
        return $resetToken;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Reviews::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'posted_by');
    }

    public function qnaSessions(): HasMany
    {
        return $this->hasMany(QnaSession::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(QnaVote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(QnaComment::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
