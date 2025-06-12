<?php

namespace App\Http\Controllers;

use App\Models\BannerModel;
use App\Models\JenisModel;
use App\Models\ParameterUjiModel;
use App\Models\PengunjungModel;
use App\Models\StatusModel;
use App\Models\StatusTransaksiProdukModel;
use App\Models\TransaksiModel;
use App\Models\TransaksiProdukModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $data_input = PengunjungModel::create([
            'id_user' => 0,
        ]);
        $param_kode_sample = $request->kode_sampel;
        $status_all = StatusModel::orderBy('status.created_at', 'DESC')->get();

        $toptitle = 'Landing';
        $title = 'Data Landing';
        $subtitle = 'Data Landing';

        $data_banner = BannerModel::latest()
            ->take(3)
            ->orderby('id', 'DESC')
            ->get();

        $data_jenis_pengujian = JenisModel::with('parameter_uji')
            ->get();

        $status_pengujian = array();

        $transaksi = TransaksiModel::where('kode_sampel', $request->kode_sampel)->first();

        if ($transaksi != null) {
            $status_pengujian = StatusTransaksiProdukModel::where('id_status', $transaksi->id)->get();
        }

        return view('welcome', compact(
            'toptitle',
            'title',
            'subtitle',
            'data_banner',
            'data_jenis_pengujian',
            // 'status_all',
            // 'status_sama',
            'status_pengujian',
            'param_kode_sample',
        ));
    }

    public function lacak($kode_sampel)
    {
        echo $kode_sampel;
        die();

        $toptitle = 'Landing';
        $title = 'Data Landing';
        $subtitle = 'Data Landing';

        $data_banner = BannerModel::latest()
            ->take(3)
            ->orderby('id', 'DESC')
            ->get();

        $data_jenis_pengujian = JenisModel::with('parameter_uji')
            ->get();

        return view('welcome', compact(
            'toptitle',
            'title',
            'subtitle',
            'data_banner',
            'data_jenis_pengujian',
        ));
    }

    public function cetak_permintaan_pengujian($id)
    {
        $toptitle = 'Fitur';
        $title = 'Pengajuan';
        $subtitle = 'Detail Pengajuan';

        $all_data = TransaksiModel::with('transaksi_produk.parameter_uji')->where('id', $id)->first();
        $data_parameter_uji = ParameterUjiModel::where('id_jenis', $all_data->id_jenis)->get();

        return view('pengguna.pengajuan.detail', compact(
            'toptitle',
            'title',
            'subtitle',
            'all_data',
            'data_parameter_uji',
        ));
    }
}
