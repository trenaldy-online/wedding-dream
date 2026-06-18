<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class WeddingSyncV2ExportModuleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public string $module
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        Artisan::call('wedding:sync-v2-export-web', [
            '--module' => $this->module,
        ]);

        Artisan::call('wedding:sync-v2-apply-dropdowns');
    }
}
