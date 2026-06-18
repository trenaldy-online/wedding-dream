<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'wedding_profile_id',
        'wedding_event_id',
        'category',
        'item_name',
        'estimated_amount',
        'actual_amount',
        'payment_status',
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
        'estimated_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
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
