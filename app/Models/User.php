<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'mobile',
        'timezone',
        'telegram_id',
        'password_raw',
        'login_attempts',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            $user->password = bcrypt($user->password);
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_raw',
        'remember_token',
    ];

    /**
     * @return HasMany
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(ReminderModel::class);
    }

    /**
     * @return HasMany
     */
    public function telegramEntity(): HasMany
    {
        return $this->hasMany(TelegramModel::class);
    }
}
