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
        // $kode_sampel = TransaksiProdukModel::where('kode_sampel', $request->kode_sampel)->first();

        // $id_transaksi_produk = 0;

        // if ($request->kode_sampel != null) {
        //     if ($kode_sampel != null) {

        //         $id_transaksi_produk = $kode_sampel->id;
        // if ($kode_sampel != null) {
        //     $id_transaksi_produk = $kode_sampel->id;
        // }
        //     }
        // }

        $toptitle = 'Landing';
        $title = 'Data Landing';
        $subtitle = 'Data Landing';

        $data_banner = BannerModel::latest()
            ->take(3)
            ->orderby('id', 'DESC')
            ->get();

        $data_jenis_pengujian = JenisModel::with('parameter_uji')
            ->get();

        // $status_pengujian = StatusTransaksiProdukModel::leftJoin('transaksi_produk', function ($join) {
        //     $join->on('status_transaksi_produk.id_transaksi_produk', '=', 'transaksi_produk.id');
        // })
        //     ->leftJoin('status', function ($join) {
        //         $join->on('status_transaksi_produk.id_status', '=', 'status.id');
        //     })
        //     ->where('transaksi_produk.id', $id_transaksi_produk)
        //     ->get([
        //         'transaksi_produk.id as id',
        //         'transaksi_produk.nama as nama',
        //         'transaksi_produk.jumlah as jumlah',
        //         'status.nama as nama_status',
        //         'status_transaksi_produk.created_at as tanggal',
        //     ]);

        // echo $data_jenis_pengujian;
        // die();

        // $status_pengujian = StatusTransaksiProdukModel::leftJoin('transaksi_produk', 'status_transaksi_produk.id_transaksi_produk', '=', 'transaksi_produk.id')
        //     ->leftJoin('status', 'status_transaksi_produk.id_status', '=', 'status.id')
        //     ->where('transaksi_produk.id', $id_transaksi_produk)
        //     ->groupBy('status_transaksi_produk.id_status')
        //     ->select('transaksi_produk.id as id', 'transaksi_produk.nama as nama', 'transaksi_produk.jumlah as jumlah', 'status.nama as nama_status', 'status_transaksi_produk.created_at as tanggal')->orderBy('status_transaksi_produk.created_at', 'DESC')
        //     ->get();

        // $status_sama = StatusModel::where('status.id', '!=', 'status_pengujian.id_status')->get();
        // $status_sama = StatusTransaksiProdukModel::where('status_transaksi_produk.id_status', '!=', 'status.id')->get();

        $transaksi = TransaksiModel::where('kode_sampel', $request->kode_sampel)->first();
        $status_pengujian = StatusTransaksiProdukModel::where('id_status', $transaksi->id)->get();

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
