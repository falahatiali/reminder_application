<?php

namespace App\Models;

use App\Helpers\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class ReminderModel extends Model
{
    use SoftDeletes;

    protected $table = 'reminders';
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFrequencyAttribute($value)
    {
        return Arr::get(Date::frequencies(), $value);
    }

    public function getDayAttribute($value)
    {
        if ($value == null) {
            return;
        };
        return Arr::get(Date::days(), $value);
    }

    public function getDateAttribute($value)
    {
        if ($value == null) {
            return;
        };
        return Date::ordinal($value);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeIsNotComplete($query)
    {
        return $query->where('is_complete', false);
    }

    public function scopeIsComplete($query)
    {
        return $query->where('is_complete', true);
    }
}
