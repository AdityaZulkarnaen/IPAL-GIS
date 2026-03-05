<?php

namespace Modules\IPAL\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KonfigurasiModel;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard IPAL.
     */
    public function index(Request $request)
    {
        $toptitle = 'IPAL';
        $title = 'Dashboard IPAL';
        $subtitle = 'Dashboard Instalasi Pengolahan Air Limbah';

        // Ambil data konfigurasi dari app utama (shared)
        $data_konfig = KonfigurasiModel::first();

        $service = [
            'data_konfig' => $data_konfig,
        ];

        return view('ipal::dashboard.index', compact(
            'toptitle',
            'title',
            'subtitle',
            'service',
        ));
    }
}
