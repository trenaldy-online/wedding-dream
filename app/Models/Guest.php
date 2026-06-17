<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    protected $fillable = [
        'wedding_profile_id',
        'wedding_event_id',
        'name',
        'phone',
        'invitation_code',
        'address',
        'group_name',
        'total_invited',
        'rsvp_status',
        'invitation_sent_at',

        // Sync fields
        'sheet_key',
        'sheet_row',
        'sync_source',
        'sheet_hash',
        'web_hash',
        'last_synced_at',
        'last_checked_at',
        'is_dummy',
        'sync_note',
    
        'rsvp_count',
        'rsvp_confirmed_at',
        'rsvp_note',
        'attendance_status',
        'actual_attendance_count',
        'checked_in_at',
        'envelope_amount',
        'souvenir_status',
        'souvenir_count',
        'sheet_updated_at',];

    protected $casts = [
        'invitation_sent_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'is_dummy' => 'boolean',
    
        'rsvp_count' => 'integer',
        'rsvp_confirmed_at' => 'datetime',
        'attendance_status' => 'string',
        'actual_attendance_count' => 'integer',
        'checked_in_at' => 'datetime',
        'envelope_amount' => 'integer',
        'souvenir_status' => 'string',
        'souvenir_count' => 'integer',
        'sheet_updated_at' => 'datetime',];

    public function weddingProfile(): BelongsTo
    {
        return $this->belongsTo(WeddingProfile::class);
    }

    public function weddingEvent(): BelongsTo
    {
        return $this->belongsTo(WeddingEvent::class);
    }

    public function guestLink()
    {
        return $this->hasOne(GuestLink::class);
    }

    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '8')) {
            return '62' . $phone;
        }

        return $phone;
    }
}
