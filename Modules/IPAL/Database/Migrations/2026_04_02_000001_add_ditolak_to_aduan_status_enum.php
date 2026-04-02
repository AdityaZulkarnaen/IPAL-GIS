<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE aduan MODIFY COLUMN status_aduan ENUM('masuk','verifikasi','proses','ditolak','selesai') NOT NULL DEFAULT 'masuk'");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::table('aduan')
                ->where('status_aduan', 'ditolak')
                ->update(['status_aduan' => 'verifikasi']);

            DB::statement("ALTER TABLE aduan MODIFY COLUMN status_aduan ENUM('masuk','verifikasi','proses','selesai') NOT NULL DEFAULT 'masuk'");
        }
    }
};
