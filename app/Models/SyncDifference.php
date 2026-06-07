<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncDifference extends Model
{
    protected $fillable = [
        'module',
        'sheet_name',
        'record_key',
        'web_model',
        'web_id',
        'sheet_row',
        'difference_type',
        'sheet_payload',
        'web_payload',
        'field_differences',
        'status',
        'note',
        'checked_at',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'sheet_payload' => 'array',
        'web_payload' => 'array',
        'field_differences' => 'array',
        'checked_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
