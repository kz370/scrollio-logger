<?php

namespace Kz370\ScollioLogger\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogEntry extends Model
{
    protected $table = 'scollio_logs';

    public $timestamps = false;

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'level',
        'message',
        'location',
        'file',
        'line',
        'context',
        'channel',
        'ip_address',
        'user_agent',
        'user_id',
        'created_at',
    ];

    public function user(): BelongsTo
    {
        $userModel = config('auth.providers.users.model');
        return $this->belongsTo($userModel, 'user_id');
    }

    public function getLevelColorAttribute(): string
    {
        return match ($this->level) {
            'emergency' => 'red-900',
            'alert'     => 'red-700',
            'critical'  => 'red-600',
            'error'     => 'red-500',
            'warning'   => 'yellow-500',
            'notice'    => 'blue-400',
            'info'      => 'blue-500',
            'debug'     => 'gray-500',
            default     => 'gray-400',
        };
    }
}
