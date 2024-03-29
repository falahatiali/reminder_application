<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramModel extends Model
{
    use HasFactory;

    public const TYPE = [
        'CALLBACK_QUERY' => 'callback_query',
        'MESSAGE' => 'message'
    ];

    public const REMINDER_TYPE = [
        'create_new' => 'front',
        'front' => 'backend',
        'backend' => 'body',
        'body' => 'additional_text',
        'additional_text' => 'frequency',
        'daily' => 'daily',
        'weekly' => 'weekly',
        'monthly' => 'monthly',
        'yearly' => 'yearly',
    ];
    protected $guarded = [];

    protected $casts = [
        'telegram' => 'json'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNotFinish($query)
    {
        return $query->where('finish', false);
    }

    public function scopeFinish($query)
    {
        return $query->where('finish', true);
    }
}
