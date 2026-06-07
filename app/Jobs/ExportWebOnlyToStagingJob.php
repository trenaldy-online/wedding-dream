<?php

namespace App\Jobs;

use App\Models\SyncDifference;
use App\Services\WeddingSync\WebToStagingSheetExporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExportWebOnlyToStagingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 120;
    public int $tries = 1;

    public function __construct(
        public string $module = 'all',
        public int $limit = 5,
        public ?int $adminId = null,
        public int $round = 1
    ) {
        $this->limit = max(1, min($this->limit, 10));
    }

    public function handle(WebToStagingSheetExporter $exporter): void
    {
        $differences = $this->pendingDifferences()
            ->take($this->limit);

        if ($differences->isEmpty()) {
            return;
        }

        $exported = 0;

        foreach ($differences as $difference) {
            try {
                $exporter->export($difference, $this->adminId);
                $exported++;
            } catch (Throwable $e) {
                Log::error('Export web only to staging failed', [
                    'sync_difference_id' => $difference->id,
                    'module' => $difference->module,
                    'web_id' => $difference->web_id,
                    'message' => $e->getMessage(),
                ]);

                /*
                 * Jangan biarkan data error diproses berulang tanpa henti.
                 * Kalau ada error, tandai failed agar bisa dicek admin.
                 */
                $difference->update([
                    'status' => 'failed',
                    'note' => trim(($difference->note ?? '') . "\nExport staging failed: " . $e->getMessage()),
                    'checked_at' => now(),
                ]);
            }
        }

        $remaining = $this->pendingDifferences()->count();

        /*
         * Kalau masih ada sisa dan batch ini berhasil memproses data,
         * dispatch job berikutnya otomatis.
         */
        if ($exported > 0 && $remaining > 0 && $this->round < 100) {
            self::dispatch(
                module: $this->module,
                limit: $this->limit,
                adminId: $this->adminId,
                round: $this->round + 1
            )->delay(now()->addSeconds(2));
        }
    }

    private function pendingDifferences()
    {
        $query = SyncDifference::query()
            ->where('status', 'pending')
            ->where('difference_type', 'web_only');

        if ($this->module === 'events') {
            $query->where('module', 'events');
        }

        if ($this->module === 'guests') {
            $query->where('module', 'guests');
        }

        if ($this->module === 'budget_items') {
            $query->where('module', 'budget_items');
        }

        if ($this->module === 'checklist_items') {
            $query->where('module', 'checklist_items')
                ->where('sheet_name', 'INPUT_PERSIAPAN');
        }

        if ($this->module === 'documents') {
            $query->where('module', 'checklist_items')
                ->where('sheet_name', 'INPUT_DOKUMEN');
        }

        return $query
            ->orderBy('module')
            ->orderBy('web_id')
            ->get()
            ->filter(fn ($difference) => !empty($difference->web_id))
            ->unique(fn ($difference) => ($difference->web_model ?: $difference->module) . '#' . $difference->web_id)
            ->values();
    }
}
