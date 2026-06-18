<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockLegacyWeddingSync
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('wedding-sync-legacy.enabled', false)) {
            return $next($request);
        }

        $path = '/' . trim($request->path(), '/');
        $lowerPath = strtolower($path);

        $isSyncPath = preg_match('#(^|/)(sync|wedding-sync|google-sheets)(/|$)#i', $path) === 1;
        $isSyncV2Path = str_contains($lowerPath, 'sync-v2') || str_contains($lowerPath, 'wedding-sync-v2');

        if ($isSyncPath && !$isSyncV2Path) {
            $message = config('wedding-sync-legacy.message', 'Sync lama sudah dinonaktifkan.');

            if ($request->expectsJson() || str_starts_with($lowerPath, '/api/')) {
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                ], 410);
            }

            if ($request->headers->has('referer')) {
                return redirect()->back()->with('warning', $message);
            }

            return response($message, 410);
        }

        return $next($request);
    }
}
