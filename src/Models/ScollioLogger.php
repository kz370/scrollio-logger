<?php

namespace Kz370\ScollioLogger\Models;

use Illuminate\Database\Eloquent\Model;

class ScollioLogger extends Model
{
    protected $table = 'scollio_logger';

    protected $fillable = [
        'method','url','ip','user_agent','headers','body','requested_at','responded_at',
        'duration_ms','status_code','response_headers','response_body','exception_message',
        'exception_file','exception_line','exception_trace','user_id','session_id','request_id',
        'route_action'
    ];

    protected $casts = [
        'headers' => 'array',
        'body' => 'array',
        'response_headers' => 'array',
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];
}
