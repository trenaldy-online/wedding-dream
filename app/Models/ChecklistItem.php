<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = [
        'wedding_profile_id',
        'wedding_event_id',
        'title',
        'category',
        'assigned_to',
        'status',
        'due_date',
        'note',
        'completed_at',

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
    
        'priority',];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'is_dummy' => 'boolean',
    ];

    public function weddingProfile(): BelongsTo
    {
        return $this->belongsTo(WeddingProfile::class);
    }

    public function weddingEvent(): BelongsTo
    {
        return $this->belongsTo(WeddingEvent::class);
    }
}
