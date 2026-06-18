<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Wedding Sync V2
    |--------------------------------------------------------------------------
    |
    | Config ini disiapkan untuk sinkronisasi baru:
    | - Web dan Google Sheet sama-sama valid sebagai sumber data.
    | - Perubahan terbaru boleh menimpa data lama.
    | - Setiap overwrite tetap dicatat untuk rollback.
    | - Sync lama tidak disentuh oleh config ini.
    |
    */

    'spreadsheet_id' => env('GOOGLE_SHEET_ID_V2'),

    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),

    'webhook' => [
        'token' => env('WEDDING_SYNC_V2_WEBHOOK_TOKEN'),
    ],

    'safe_delete' => [
        'enabled' => env('WEDDING_SYNC_V2_SAFE_DELETE_ENABLED', true),
    ],

    'auto_refresh_dropdowns' => [
        'enabled' => env('WEDDING_SYNC_V2_AUTO_REFRESH_DROPDOWNS', true),
    ],

    'auto_export_from_web' => [
        'enabled' => env('WEDDING_SYNC_V2_AUTO_EXPORT_WEB', true),
        'use_queue' => env('WEDDING_SYNC_V2_WEB_EXPORT_QUEUE', false),
    ],

    'strategy' => [
        'mode' => 'last_write_wins_with_audit_log',
        'conflict_review_threshold_seconds' => 10,
        'allow_auto_create_from_sheet' => true,
        'allow_auto_create_from_web' => true,
        'allow_auto_update_from_sheet' => true,
        'allow_auto_update_from_web' => true,
        'allow_delete_sync' => false,
    ],

    'system_columns' => [
        'web_id',
        'sync_key',
        'last_modified_at',
        'last_modified_by',
        'last_modified_source',
        'last_synced_at',
        'row_hash',
    ],

    'modules' => [

        'persiapan' => [
            'sheet' => 'INPUT_PERSIAPAN',
            'model' => App\Models\ChecklistItem::class,
            'type' => 'checklist',
            'filter' => [
                'category_not_in' => ['Dokumen', 'Dokumen Nikah'],
            ],
            'headers' => [
                'acara',
                'kategori',
                'tugas',
                'pic',
                'prioritas',
                'deadline',
                'status',
                'progress_percent',
                'dependensi',
                'catatan',
                'sync_action',
                'sync_status',
                'web_id',
                'sync_key',
                'last_modified_at',
                'last_modified_by',
                'last_modified_source',
                'last_synced_at',
                'row_hash',
            ],
            'mapping' => [
                'acara' => 'wedding_event_display',
                'kategori' => 'category',
                'tugas' => 'title',
                'pic' => 'assigned_to',
                'prioritas' => 'priority',
                'deadline' => 'due_date',
                'status' => 'status',
                'progress_percent' => 'progress_percent',
                'dependensi' => 'dependency',
                'catatan' => 'note',
            ],
            'required_for_create' => [
                'acara',
                'tugas',
            ],


            'defaults' => [
                'category' => 'Persiapan',
                'priority' => 'Wajib',
                'status' => 'Belum',
                'progress_percent' => 0,
            ],
        ],

        'dokumen' => [
            'sheet' => 'INPUT_DOKUMEN',
            'model' => App\Models\ChecklistItem::class,
            'type' => 'checklist',
            'filter' => [
                'category_in' => ['Dokumen', 'Dokumen Nikah'],
            ],
            'headers' => [
                'acara',
                'kategori',
                'dokumen',
                'pic',
                'deadline',
                'status',
                'progress_percent',
                'catatan',
                'sync_action',
                'sync_status',
                'web_id',
                'sync_key',
                'last_modified_at',
                'last_modified_by',
                'last_modified_source',
                'last_synced_at',
                'row_hash',
            ],
            'mapping' => [
                'acara' => 'wedding_event_display',
                'kategori' => 'category',
                'dokumen' => 'title',
                'pic' => 'assigned_to',
                'deadline' => 'due_date',
                'status' => 'status',
                'progress_percent' => 'progress_percent',
                'catatan' => 'note',
            ],
            'required_for_create' => [
                'acara',
                'dokumen',
            ],


            'defaults' => [
                'category' => 'Dokumen Nikah',
                'priority' => null,
                'status' => 'Belum',
                'progress_percent' => 0,
            ],
            'forced_values' => [
                'category' => 'Dokumen Nikah',
                'priority' => null,
            ],
        ],

        'budget_cpp' => [
            'sheet' => 'BUDGET_CPP',
            'model' => App\Models\BudgetItem::class,
            'type' => 'budget',
            'event_side' => 'CPP',
            'headers' => [
                'kategori',
                'item',
                'estimasi',
                'aktual',
                'status_bayar',
                'catatan',
                'sync_action',
                'sync_status',
                'web_id',
                'sync_key',
                'last_modified_at',
                'last_modified_by',
                'last_modified_source',
                'last_synced_at',
                'row_hash',
            ],
            'mapping' => [
                'kategori' => 'category',
                'item' => 'item_name',
                'estimasi' => 'estimated_amount',
                'aktual' => 'actual_amount',
                'status_bayar' => 'payment_status',
                'catatan' => 'note',
            ],
            'required_for_create' => [
                'item',
            ],


            'defaults' => [
                'estimated_amount' => 0,
                'actual_amount' => 0,
                'payment_status' => 'Belum Bayar',
            ],
        ],

        'budget_cpw' => [
            'sheet' => 'BUDGET_CPW',
            'model' => App\Models\BudgetItem::class,
            'type' => 'budget',
            'event_side' => 'CPW',
            'headers' => [
                'kategori',
                'item',
                'estimasi',
                'aktual',
                'status_bayar',
                'catatan',
                'sync_action',
                'sync_status',
                'web_id',
                'sync_key',
                'last_modified_at',
                'last_modified_by',
                'last_modified_source',
                'last_synced_at',
                'row_hash',
            ],
            'mapping' => [
                'kategori' => 'category',
                'item' => 'item_name',
                'estimasi' => 'estimated_amount',
                'aktual' => 'actual_amount',
                'status_bayar' => 'payment_status',
                'catatan' => 'note',
            ],
            'required_for_create' => [
                'item',
            ],


            'defaults' => [
                'estimated_amount' => 0,
                'actual_amount' => 0,
                'payment_status' => 'Belum Bayar',
            ],
        ],

        'tamu_cpp' => [
            'sheet' => 'TAMU_CPP',
            'model' => App\Models\Guest::class,
            'type' => 'guest',
            'event_side' => 'CPP',
            'headers' => [
                'nama',
                'no_wa',
                'alamat',
                'grup',
                'jumlah_undangan',
                'rsvp_status',
                'rsvp_count',
                'rsvp_note',
                'invitation_status',
                'attendance_status',
                'actual_attendance_count',
                'envelope_amount',
                'souvenir_status',
                'souvenir_count',
                'catatan_sync',
                'sync_action',
                'sync_status',
                'web_id',
                'sync_key',
                'last_modified_at',
                'last_modified_by',
                'last_modified_source',
                'last_synced_at',
                'row_hash',
            ],
            'mapping' => [
                'nama' => 'name',
                'no_wa' => 'phone',
                'alamat' => 'address',
                'grup' => 'group_name',
                'jumlah_undangan' => 'total_invited',
                'rsvp_status' => 'rsvp_status',
                'rsvp_count' => 'rsvp_count',
                'rsvp_note' => 'rsvp_note',
                'invitation_status' => 'invitation_status',
                'attendance_status' => 'attendance_status',
                'actual_attendance_count' => 'actual_attendance_count',
                'envelope_amount' => 'envelope_amount',
                'souvenir_status' => 'souvenir_status',
                'souvenir_count' => 'souvenir_count',
                'catatan_sync' => 'sync_note',
            ],
            'required_for_create' => [
                'nama',
                'no_wa',
                'grup',
                'jumlah_undangan',
            ],


            'defaults' => [
                'total_invited' => 1,
                'rsvp_status' => 'pending',
                'rsvp_count' => 0,
                'invitation_status' => 'not_sent',
                'attendance_status' => 'not_arrived',
                'actual_attendance_count' => 0,
                'envelope_amount' => 0,
                'souvenir_status' => 'not_given',
                'souvenir_count' => 0,
            ],
        ],

        'tamu_cpw' => [
            'sheet' => 'TAMU_CPW',
            'model' => App\Models\Guest::class,
            'type' => 'guest',
            'event_side' => 'CPW',
            'headers' => [
                'nama',
                'no_wa',
                'alamat',
                'grup',
                'jumlah_undangan',
                'rsvp_status',
                'rsvp_count',
                'rsvp_note',
                'invitation_status',
                'attendance_status',
                'actual_attendance_count',
                'envelope_amount',
                'souvenir_status',
                'souvenir_count',
                'catatan_sync',
                'sync_action',
                'sync_status',
                'web_id',
                'sync_key',
                'last_modified_at',
                'last_modified_by',
                'last_modified_source',
                'last_synced_at',
                'row_hash',
            ],
            'mapping' => [
                'nama' => 'name',
                'no_wa' => 'phone',
                'alamat' => 'address',
                'grup' => 'group_name',
                'jumlah_undangan' => 'total_invited',
                'rsvp_status' => 'rsvp_status',
                'rsvp_count' => 'rsvp_count',
                'rsvp_note' => 'rsvp_note',
                'invitation_status' => 'invitation_status',
                'attendance_status' => 'attendance_status',
                'actual_attendance_count' => 'actual_attendance_count',
                'envelope_amount' => 'envelope_amount',
                'souvenir_status' => 'souvenir_status',
                'souvenir_count' => 'souvenir_count',
                'catatan_sync' => 'sync_note',
            ],
            'required_for_create' => [
                'nama',
                'no_wa',
                'grup',
                'jumlah_undangan',
            ],


            'defaults' => [
                'total_invited' => 1,
                'rsvp_status' => 'pending',
                'rsvp_count' => 0,
                'invitation_status' => 'not_sent',
                'attendance_status' => 'not_arrived',
                'actual_attendance_count' => 0,
                'envelope_amount' => 0,
                'souvenir_status' => 'not_given',
                'souvenir_count' => 0,
            ],
        ],
    ],

    'system_sheets' => [

        'sync_log' => [
            'sheet' => 'SYNC_LOG',
            'headers' => [
                'waktu',
                'module',
                'source',
                'direction',
                'action',
                'web_id',
                'sync_key',
                'item',
                'field',
                'old_value',
                'new_value',
                'status',
                'message',
            ],
        ],

        'rollback_log' => [
            'sheet' => 'ROLLBACK_LOG',
            'headers' => [
                'waktu',
                'module',
                'web_id',
                'sync_key',
                'item',
                'rollback_by',
                'rollback_from',
                'rollback_to',
                'status',
                'message',
            ],
        ],

        'sync_conflicts' => [
            'sheet' => 'SYNC_CONFLICTS',
            'headers' => [
                'detected_at',
                'module',
                'web_id',
                'sync_key',
                'item',
                'field',
                'web_value',
                'sheet_value',
                'web_modified_at',
                'sheet_modified_at',
                'recommended_winner',
                'status',
                'message',
            ],
        ],
    ],
];
