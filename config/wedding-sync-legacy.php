<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Legacy Wedding Sync
    |--------------------------------------------------------------------------
    |
    | Sync lama sengaja dinonaktifkan karena Sync V2 sudah menjadi alur utama.
    | Kode lama tidak dihapus dulu agar masih bisa dibandingkan/rollback bila perlu.
    |
    */

    'enabled' => env('WEDDING_SYNC_LEGACY_ENABLED', false),

    'message' => 'Sync lama sudah dinonaktifkan. Gunakan Wedding Sync V2.',

];
