<?php

namespace App\Http\Controllers;

use App\Models\SyncDifference;
use App\Models\SyncRun;
use App\Jobs\ExportWebOnlyToStagingJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Services\WeddingSync\SheetToWebImporter;
use App\Services\WeddingSync\WebToStagingSheetExporter;
use Throwable;

class SyncController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $module = $request->input('module', 'all');
        $differenceType = $request->input('difference_type', 'all');
        $sheetName = $request->input('sheet_name', 'all');

        $query = SyncDifference::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($module !== 'all') {
            $query->where('module', $module);
        }

        if ($differenceType !== 'all') {
            $query->where('difference_type', $differenceType);
        }

        if ($sheetName !== 'all') {
            $query->where('sheet_name', $sheetName);
        }

        $differences = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->latest('checked_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summaryByStatus = SyncDifference::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $summaryByType = SyncDifference::query()
            ->where('status', 'pending')
            ->selectRaw('difference_type, COUNT(*) as total')
            ->groupBy('difference_type')
            ->orderBy('difference_type')
            ->get();

        $summaryBySheet = SyncDifference::query()
            ->where('status', 'pending')
            ->selectRaw('sheet_name, difference_type, COUNT(*) as total')
            ->groupBy('sheet_name', 'difference_type')
            ->orderBy('sheet_name')
            ->orderBy('difference_type')
            ->get();

        $runs = SyncRun::query()
            ->latest()
            ->take(5)
            ->get();

        $modules = SyncDifference::query()
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');
        /*
         * Dibuat statis agar opsi tetap muncul meskipun saat ini
         * belum ada data dengan tipe tertentu, misalnya 'different'.
         */
        $differenceTypes = collect([
            'sheet_only',
            'web_only',
            'different',
            'conflict',
            'dummy',
        ]);

        $sheetNames = SyncDifference::query()
            ->whereNotNull('sheet_name')
            ->distinct()
            ->orderBy('sheet_name')
            ->pluck('sheet_name');

        return view('sync.index', compact(
            'differences',
            'summaryByStatus',
            'summaryByType',
            'summaryBySheet',
            'runs',
            'modules',
            'differenceTypes',
            'sheetNames',
            'status',
            'module',
            'differenceType',
            'sheetName'
        ));
    }

    public function run(Request $request)
    {
        $module = $request->input('module', 'all');

        $params = [
            '--fresh' => true,
        ];

        if ($module !== 'all') {
            $params['--modules'] = $module;
        }

        $exitCode = Artisan::call('wedding:sync-reconcile', $params);
        $output = Artisan::output();

        if ($exitCode !== 0) {
            return redirect()
                ->route('sync.index')
                ->with('error', 'Reconcile gagal dijalankan.')
                ->with('sync_output', $output);
        }

        return redirect()
            ->route('sync.index')
            ->with('success', 'Reconcile berhasil dijalankan.')
            ->with('sync_output', $output);
    }

    public function applySheetToWeb(
        SyncDifference $syncDifference,
        SheetToWebImporter $importer
    ) {
        try {
            $record = $importer->import($syncDifference, auth()->id());

            return redirect()
                ->route('sync.index')
                ->with('success', 'Data berhasil di-import dari Google Sheet ke Web. Web ID: ' . $record->id);
        } catch (Throwable $e) {
            return redirect()
                ->route('sync.index')
                ->with('error', 'Import Sheet → Web gagal: ' . $e->getMessage());
        }
    }

    public function exportWebToStaging(
        SyncDifference $syncDifference,
        WebToStagingSheetExporter $exporter
    ) {
        try {
            $record = $exporter->export($syncDifference, auth()->id());

            return redirect()
                ->route('sync.index')
                ->with('success', 'Data berhasil dikirim ke WEB_EXPORT staging sheet. Web ID: ' . $record->id);
        } catch (\Throwable $e) {
            return redirect()
                ->route('sync.index')
                ->with('error', 'Export ke staging gagal: ' . $e->getMessage());
        }
    }

    public function exportAllWebToStaging(Request $request)
    {
        $module = $request->input('module', 'all');

        /*
         * Tetap batch kecil, tetapi dijalankan otomatis oleh queue worker.
         */
        $limit = (int) $request->input('limit', 5);
        $limit = max(1, min($limit, 10));

        ExportWebOnlyToStagingJob::dispatch(
            module: $module,
            limit: $limit,
            adminId: auth()->id()
        );

        return redirect()
            ->route('sync.index')
            ->with(
                'success',
                'Export ke WEB_EXPORT staging sedang dijalankan di backend. Sistem akan memproses ' .
                $limit .
                ' data per batch sampai selesai. Pastikan queue worker aktif.'
            );
    }

    public function applyAllSheetToWeb(Request $request, SheetToWebImporter $importer)
    {
        $module = $request->input('module', 'all');

        $query = SyncDifference::query()
            ->where('status', 'pending')
            ->where('difference_type', 'sheet_only');

        if ($module === 'events') {
            $query->where('module', 'events');
        }

        if ($module === 'guests') {
            $query->where('module', 'guests');
        }

        if ($module === 'budget_items') {
            $query->where('module', 'budget_items');
        }

        if ($module === 'checklist_items') {
            $query->where('module', 'checklist_items')
                ->where('sheet_name', 'INPUT_PERSIAPAN');
        }

        if ($module === 'documents') {
            $query->where('module', 'checklist_items')
                ->where('sheet_name', 'INPUT_DOKUMEN');
        }

        $differences = $query
            ->orderBy('sheet_name')
            ->orderBy('sheet_row')
            ->get();

        if ($differences->isEmpty()) {
            return redirect()
                ->route('sync.index')
                ->with('success', 'Tidak ada data sheet_only yang perlu di-import.');
        }

        $imported = 0;
        $errors = [];

        foreach ($differences as $difference) {
            try {
                $importer->import($difference, auth()->id());
                $imported++;
            } catch (Throwable $e) {
                $errors[] = 'ID ' . $difference->id . ': ' . $e->getMessage();
            }
        }

        $message = "Import selesai. Berhasil: {$imported}. Gagal: " . count($errors) . ".";

        if (!empty($errors)) {
            return redirect()
                ->route('sync.index')
                ->with('error', $message . ' Detail: ' . implode(' | ', array_slice($errors, 0, 5)));
        }

        return redirect()
            ->route('sync.index')
            ->with('success', $message);
    }

    public function resolve(SyncDifference $syncDifference)
    {
        $syncDifference->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
            'note' => trim(($syncDifference->note ?? '') . "\nDitandai selesai oleh admin."),
        ]);

        return redirect()
            ->route('sync.index')
            ->with('success', 'Perbedaan berhasil ditandai selesai.');
    }

    public function ignore(SyncDifference $syncDifference)
    {
        $syncDifference->update([
            'status' => 'ignored',
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
            'note' => trim(($syncDifference->note ?? '') . "\nDiabaikan oleh admin."),
        ]);

        return redirect()
            ->route('sync.index')
            ->with('success', 'Perbedaan berhasil diabaikan.');
    }
}
