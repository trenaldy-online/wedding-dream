<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            /*
             * SISTEM 2: RSVP dari tamu
             * rsvp_status sudah ada.
             * rsvp_count = jumlah orang yang tamu konfirmasi akan hadir.
             */
            if (! Schema::hasColumn('guests', 'rsvp_count')) {
                $table->unsignedSmallInteger('rsvp_count')
                    ->default(0)
                    ->after('rsvp_status');
            }

            if (! Schema::hasColumn('guests', 'rsvp_confirmed_at')) {
                $table->timestamp('rsvp_confirmed_at')
                    ->nullable()
                    ->after('rsvp_count');
            }

            if (! Schema::hasColumn('guests', 'rsvp_note')) {
                $table->text('rsvp_note')
                    ->nullable()
                    ->after('rsvp_confirmed_at');
            }

            /*
             * SISTEM 3: Data hari-H / setelah acara
             */
            if (! Schema::hasColumn('guests', 'attendance_status')) {
                $table->string('attendance_status', 30)
                    ->default('not_arrived')
                    ->after('invitation_sent_at');
            }

            if (! Schema::hasColumn('guests', 'actual_attendance_count')) {
                $table->unsignedSmallInteger('actual_attendance_count')
                    ->default(0)
                    ->after('attendance_status');
            }

            if (! Schema::hasColumn('guests', 'checked_in_at')) {
                $table->timestamp('checked_in_at')
                    ->nullable()
                    ->after('actual_attendance_count');
            }

            if (! Schema::hasColumn('guests', 'envelope_amount')) {
                $table->unsignedBigInteger('envelope_amount')
                    ->default(0)
                    ->after('checked_in_at');
            }

            if (! Schema::hasColumn('guests', 'souvenir_status')) {
                $table->string('souvenir_status', 30)
                    ->default('not_given')
                    ->after('envelope_amount');
            }

            if (! Schema::hasColumn('guests', 'souvenir_count')) {
                $table->unsignedSmallInteger('souvenir_count')
                    ->default(0)
                    ->after('souvenir_status');
            }

            /*
             * Timestamp dari Google Sheet.
             * updated_at tetap dipakai sebagai waktu update dari sisi web.
             */
            if (! Schema::hasColumn('guests', 'sheet_updated_at')) {
                $table->timestamp('sheet_updated_at')
                    ->nullable()
                    ->after('last_checked_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $columns = [
                'rsvp_count',
                'rsvp_confirmed_at',
                'rsvp_note',
                'attendance_status',
                'actual_attendance_count',
                'checked_in_at',
                'envelope_amount',
                'souvenir_status',
                'souvenir_count',
                'sheet_updated_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('guests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};