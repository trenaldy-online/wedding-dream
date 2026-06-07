<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeddingEvent extends Model
{
    protected $fillable = [
        'wedding_profile_id',
        'event_name',
        'event_side',
        'event_date',
        'venue_name',
        'venue_address',
        'note',

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
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'last_synced_at' => 'datetime',
        'last_checked_at' => 'datetime',
        'is_dummy' => 'boolean',
    ];

    public function weddingProfile(): BelongsTo
    {
        return $this->belongsTo(WeddingProfile::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }
}
