<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeddingSyncV2\WeddingSyncV2SheetImporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class WeddingSyncV2WebhookController extends Controller
{
    public function sheet(Request $request, WeddingSyncV2SheetImporter $importer): JsonResponse
    {
        $configuredToken = (string) config('wedding-sync-v2.webhook.token');

        if ($configuredToken === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Webhook token belum diset di WEDDING_SYNC_V2_WEBHOOK_TOKEN.',
            ], 500);
        }

        $providedToken = (string) ($request->header('X-Wedding-Sync-Token') ?: $request->input('token'));

        if (!hash_equals($configuredToken, $providedToken)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid webhook token.',
            ], 403);
        }

        $validated = $request->validate([
            'sheet' => ['required', 'string'],
            'row_number' => ['required', 'integer', 'min:2'],
            'edited_by' => ['nullable', 'string'],
            'edited_at' => ['nullable', 'string'],
            'dry_run' => ['nullable', 'boolean'],
        ]);

        try {
            app()->instance('wedding-sync-v2.suppress-web-export', true);

            $result = $importer->importSheetRow(
                $validated['sheet'],
                (int) $validated['row_number'],
                (bool) ($validated['dry_run'] ?? false)
            );

            return response()->json([
                'ok' => true,
                'message' => 'Webhook processed.',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 500);
        } finally {
            app()->forgetInstance('wedding-sync-v2.suppress-web-export');
        }
    }
}
