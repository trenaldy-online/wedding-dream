<?php

namespace App\Observers;

use App\Models\BudgetItem;
use App\Models\ChecklistItem;
use App\Models\Guest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Throwable;
use App\Jobs\WeddingSyncV2ExportModuleJob;

class WeddingSyncV2AutoExportObserver
{
    public function saved(Model $model): void
    {
        $this->autoExport($model);
    }

    public function deleted(Model $model): void
    {
        $this->autoExport($model);
    }

    private function autoExport(Model $model): void
    {
        if (!config('wedding-sync-v2.auto_export_from_web.enabled', true)) {
            return;
        }

        if (app()->runningInConsole()) {
            return;
        }

        if (app()->bound('wedding-sync-v2.suppress-web-export')) {
            return;
        }

        $modules = $this->modulesFor($model);

        foreach ($modules as $module) {
            try {
                if (config('wedding-sync-v2.auto_export_from_web.use_queue', false)) {
                    WeddingSyncV2ExportModuleJob::dispatch($module);
                } else {
                    Artisan::call('wedding:sync-v2-export-web', [
                        '--module' => $module,
                    ]);
                }
            } catch (Throwable $e) {
                report($e);
            }
        }

        $this->refreshDropdownsAfterWebChange($model);
    }


    private function refreshDropdownsAfterWebChange(Model $model): void
    {
        if (! config('wedding-sync-v2.auto_refresh_dropdowns.enabled', true)) {
            return;
        }

        $hasFlexibleOptions = $model instanceof ChecklistItem
            || $model instanceof BudgetItem
            || $model instanceof Guest;

        if (! $hasFlexibleOptions) {
            return;
        }

        try {
            Artisan::call('wedding:sync-v2-apply-dropdowns');
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function modulesFor(Model $model): array
    {
        if ($model instanceof ChecklistItem) {
            return $this->isDocumentChecklist($model)
                ? ['dokumen']
                : ['persiapan'];
        }

        if ($model instanceof BudgetItem) {
            $side = $this->eventSideOf($model);

            if ($side === 'CPP') {
                return ['budget_cpp'];
            }

            if ($side === 'CPW') {
                return ['budget_cpw'];
            }

            return ['budget_cpp', 'budget_cpw'];
        }

        if ($model instanceof Guest) {
            $side = $this->eventSideOf($model);

            if ($side === 'CPP') {
                return ['tamu_cpp'];
            }

            if ($side === 'CPW') {
                return ['tamu_cpw'];
            }

            return ['tamu_cpp', 'tamu_cpw'];
        }

        return [];
    }

    private function isDocumentChecklist(ChecklistItem $model): bool
    {
        $category = strtolower(trim((string) $model->getAttribute('category')));
        $category = str_replace(['_', '-'], ' ', $category);
        $category = preg_replace('/\s+/', ' ', $category);

        return in_array($category, ['dokumen', 'dokumen nikah'], true);
    }

    private function eventSideOf(Model $model): ?string
    {
        if (!$model->relationLoaded('weddingEvent')) {
            try {
                $model->load('weddingEvent');
            } catch (Throwable $e) {
                // ignore
            }
        }

        $event = $model->getRelationValue('weddingEvent');

        $candidates = [
            $model->getAttribute('event_side'),
            $model->getAttribute('side'),
            $event?->getAttribute('event_side'),
            $event?->getAttribute('side'),
            $event?->getAttribute('pihak'),
            $event?->getAttribute('event_key'),
            $event?->getAttribute('slug'),
            $event?->getAttribute('name'),
            $event?->getAttribute('event_name'),
            $event?->getAttribute('title'),
        ];

        foreach ($candidates as $candidate) {
            $text = strtoupper((string) ($candidate ?? ''));

            if ($text === '') {
                continue;
            }

            if (str_contains($text, 'CPW') || str_contains($text, 'WANITA') || str_contains($text, 'CEWE')) {
                return 'CPW';
            }

            if (str_contains($text, 'CPP') || str_contains($text, 'PRIA') || str_contains($text, 'COWO')) {
                return 'CPP';
            }
        }

        return null;
    }
}
