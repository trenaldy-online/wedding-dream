<?php

use App\Http\Controllers\Api\WeddingSyncV2WebhookController;

use Illuminate\Support\Facades\Route;


Route::post('/wedding-sync-v2/sheet-webhook', [WeddingSyncV2WebhookController::class, 'sheet'])
    ->name('api.wedding-sync-v2.sheet-webhook');

