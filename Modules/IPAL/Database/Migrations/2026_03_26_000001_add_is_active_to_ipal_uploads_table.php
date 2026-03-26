<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ipal_uploads', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('status');
            $table->index(['tipe', 'status', 'is_active']);
        });

        DB::table('ipal_uploads')
            ->where('status', 'completed')
            ->update(['is_active' => false]);

        foreach (['manhole', 'pipe'] as $tipe) {
            $latestId = DB::table('ipal_uploads')
                ->where('tipe', $tipe)
                ->where('status', 'completed')
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->value('id');

            if ($latestId !== null) {
                DB::table('ipal_uploads')
                    ->where('id', $latestId)
                    ->update(['is_active' => true]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('ipal_uploads', function (Blueprint $table) {
            $table->dropIndex('ipal_uploads_tipe_status_is_active_index');
            $table->dropColumn('is_active');
        });
    }
};
