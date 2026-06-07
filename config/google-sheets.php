<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Sheet utama project wedding
    |--------------------------------------------------------------------------
    |
    | spreadsheet_id diambil dari link Google Sheet:
    | https://docs.google.com/spreadsheets/d/{SPREADSHEET_ID}/edit
    |
    */

    'spreadsheet_id' => env('GOOGLE_SHEET_ID'),

    /*
    |--------------------------------------------------------------------------
    | Service Account JSON
    |--------------------------------------------------------------------------
    |
    | File ini nanti berasal dari Google Cloud.
    | Jangan upload file JSON ini ke GitHub.
    |
    */

    'service_account_json' => storage_path(
        str_replace('storage/', '', env('GOOGLE_SERVICE_ACCOUNT_JSON', 'app/google/service-account.json'))
    ),

    /*
    |--------------------------------------------------------------------------
    | Nama sheet/tab yang dipakai untuk sinkronisasi
    |--------------------------------------------------------------------------
    */

    'sheets' => [
        'events' => 'MASTER_EVENT',
        'guests_cpw' => 'INPUT_TAMU_CPW',
        'guests_cpp' => 'INPUT_TAMU_CPP',
        'budget' => 'INPUT_BUDGET',
        'checklist' => 'INPUT_PERSIAPAN',
        'documents' => 'INPUT_DOKUMEN',
        'tracking_export' => 'EXPORT_TAMU_TRACKING',
        'sync_log' => 'SYNC_LOG',
    ],
];
