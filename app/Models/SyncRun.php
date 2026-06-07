<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncRun extends Model
{
    protected $fillable = [
        'run_type',
        'status',
        'started_at',
        'finished_at',
        'total_sheet_rows',
        'total_web_rows',
        'total_same',
        'total_sheet_only',
        'total_web_only',
        'total_different',
        'total_conflict',
        'total_dummy',
        'total_errors',
        'summary',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'summary' => 'array',
    ];

    public function markCompleted(array $summary = []): void
    {
        $this->update([
            'status' => 'completed',
            'finished_at' => now(),
            'summary' => $summary,
        ]);
    }

    public function markFailed(string $message): void
    {
        $this->update([
            'status' => 'failed',
            'finished_at' => now(),
            'error_message' => $message,
        ]);
    }
}
