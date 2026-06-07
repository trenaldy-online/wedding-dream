<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationSection extends Model
{
    protected $fillable = [
        'wedding_profile_id',
        'section_key',
        'section_label',
        'is_active',
        'sort_order',
        'content',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'content' => 'array',
    ];

    public function weddingProfile(): BelongsTo
    {
        return $this->belongsTo(WeddingProfile::class);
    }
}