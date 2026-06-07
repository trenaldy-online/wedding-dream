<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestLinkSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_link_id',
        'session_token',
        'device_hash',
        'ip_address',
        'user_agent',
        'opened_at',
        'last_seen_at',
        'open_count',
        'duration_seconds',
        'max_scroll_percent',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'open_count' => 'integer',
        'duration_seconds' => 'integer',
        'max_scroll_percent' => 'integer',
    ];

    public function guestLink()
    {
        return $this->belongsTo(GuestLink::class);
    }
}