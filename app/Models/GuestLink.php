<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_profile_id',
        'guest_id',
        'guest_name',
        'guest_slug',
        'token',
        'is_active',
        'device_warning_threshold',
        'open_count',
        'unique_device_count',
        'first_opened_at',
        'last_opened_at',
        'is_suspected_shared',
        'suspicion_reason',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_suspected_shared' => 'boolean',
        'device_warning_threshold' => 'integer',
        'open_count' => 'integer',
        'unique_device_count' => 'integer',
        'first_opened_at' => 'datetime',
        'last_opened_at' => 'datetime',
    ];

    public function weddingProfile()
    {
        return $this->belongsTo(WeddingProfile::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function sessions()
    {
        return $this->hasMany(GuestLinkSession::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('invitation.guest', [
            'slug' => $this->guest_slug,
            'code' => $this->token,
        ]);
    }

    public function refreshShareWarning(): void
    {
        $uniqueDeviceCount = $this->sessions()
            ->distinct('device_hash')
            ->count('device_hash');

        $isSuspected = $uniqueDeviceCount > $this->device_warning_threshold;

        $this->update([
            'unique_device_count' => $uniqueDeviceCount,
            'is_suspected_shared' => $isSuspected,
            'suspicion_reason' => $isSuspected
                ? 'Link dibuka dari lebih dari ' . $this->device_warning_threshold . ' perangkat berbeda.'
                : null,
        ]);
    }
}