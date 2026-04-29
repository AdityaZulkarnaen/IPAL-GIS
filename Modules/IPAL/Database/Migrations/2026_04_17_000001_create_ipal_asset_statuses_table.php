<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipal_asset_statuses', function (Blueprint $table) {
            $table->id();
            $table->enum('asset_type', ['manhole', 'pipe']);
            $table->string('asset_code');
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->string('status')->default('baik');
            $table->timestamps();

            $table->unique(['asset_type', 'asset_code']);
            $table->index(['asset_type', 'status']);
            $table->index(['asset_type', 'asset_id']);
        });

        $this->backfillStatuses();
    }

    public function down(): void
    {
        Schema::dropIfExists('ipal_asset_statuses');
    }

    private function backfillStatuses(): void
    {
        $now = now();

        $activeManholeUploadId = DB::table('ipal_uploads')
            ->where('tipe', 'manhole')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->value('id');

        if ($activeManholeUploadId !== null) {
            DB::table('ipal_manholes')
                ->where('upload_id', $activeManholeUploadId)
                ->select(['id', 'kode_manhole', 'status'])
                ->orderBy('id')
                ->chunk(1000, function ($rows) use ($now): void {
                    $payload = [];
                    foreach ($rows as $row) {
                        $payload[] = [
                            'asset_type' => 'manhole',
                            'asset_code' => (string) $row->kode_manhole,
                            'asset_id' => (int) $row->id,
                            'status' => $this->normalizeStatus($row->status),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if (!empty($payload)) {
                        DB::table('ipal_asset_statuses')->insert($payload);
                    }
                });
        }

        $activePipeUploadId = DB::table('ipal_uploads')
            ->where('tipe', 'pipe')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->value('id');

        if ($activePipeUploadId !== null) {
            DB::table('ipal_jaringan_pipa')
                ->where('upload_id', $activePipeUploadId)
                ->select(['id', 'kode_pipa', 'status'])
                ->orderBy('id')
                ->chunk(1000, function ($rows) use ($now): void {
                    $payload = [];
                    foreach ($rows as $row) {
                        $payload[] = [
                            'asset_type' => 'pipe',
                            'asset_code' => (string) $row->kode_pipa,
                            'asset_id' => (int) $row->id,
                            'status' => $this->normalizeStatus($row->status),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if (!empty($payload)) {
                        DB::table('ipal_asset_statuses')->insert($payload);
                    }
                });
        }
    }

    private function normalizeStatus($status): string
    {
        $raw = strtolower(trim((string) $status));

        if ($raw === 'aman' || $raw === 'baik') {
            return 'baik';
        }

        if ($raw === 'dalam perbaikan' || $raw === 'perbaikan') {
            return 'perbaikan';
        }

        if ($raw === 'bermasalah' || $raw === 'masalah' || $raw === 'rusak') {
            return 'rusak';
        }

        return 'baik';
    }
};
