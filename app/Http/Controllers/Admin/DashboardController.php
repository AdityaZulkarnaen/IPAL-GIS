<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\JenisModel;
use Illuminate\Http\Request;
use App\Models\TransaksiModel;
use App\Models\PengunjungModel;
use App\Http\Controllers\Controller;
use App\Models\TransaksiProdukModel;
use App\Models\StatusTransaksiProdukModel;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toptitle = 'Fitur';
        $title = 'Dashboard';
        $subtitle = 'Data Dashboard';

        $jumlah_pengguna = User::where('role', '=', 'Pengguna')->count();
        $jumlah_pengunjung = PengunjungModel::count();
        $jumlah_jenis_pengajuan = JenisModel::count();
        $jumlah_pengajuan = TransaksiModel::count();
        $jumlah_pengajuan_selesai = TransaksiModel::where('status_bayar', '=', 'Order Selesai')->count();

        // $jumlah_pengajuan_selesai = StatusTransaksiProdukModel::where('id', '=', '7')->count();
        $jumlah_pengajuan_user = TransaksiModel::where('id_user', auth()->user()->id)->count();


        $jenis_layanan = $request->jenis_layanan;
        $status_transaksi = $request->status_transaksi;
        $tgl_1 = $request->tgl_1;
        $tgl_2 = $request->tgl_2;

        $jenis_pengujian = JenisModel::orderBy('id', 'DESC')
            ->get();

        $query = TransaksiModel::with('user')
            ->orderBy('created_at', 'DESC');

        // $query = TransaksiModel::with('user')->rightJoin('transaksi_produk', 'transaksi.id', '=', 'transaksi_produk.id_transaksi')->orderBy('transaksi_produk.created_at', 'DESC');

        // $query = TransaksiModel::with('user')
        // ->leftJoin('transaksi_produk', 'transaksi.id', '=', 'transaksi_produk.id_transaksi')
        // // ->leftJoin('status_transaksi_produk', 'transaksi_produk.id', '=', 'status_transaksi_produk.id_transaksi_produk')
        // ->select('transaksi_produk.id as id', 'transaksi.id_user as id_user', 'transaksi.kegiatan as kegiatan', 'transaksi.no_dokumen as no_dokumen', 'transaksi_produk.created_at as created_at', 'transaksi.sumber as sumber', 'transaksi_produk.status_bayar as status_bayar', 'transaksi_produk.no_order as no_order', 'transaksi_produk.kode_sampel as kode_sampel', 'transaksi_produk.nama as nama', 'transaksi_produk.catatan as catatan')
        // ->orderBy('transaksi_produk.created_at', 'DESC');

        $query2 = StatusTransaksiProdukModel::where('id_transaksi_produk', 'query.id');
        // dd($query2);

        if (!is_null($jenis_layanan)) {
            $query->where('id_jenis', $jenis_layanan);
        }

        if (!is_null($status_transaksi)) {
            $query->where('status_bayar', $status_transaksi);
        }

        if (!is_null($tgl_1)) {
            $query->whereDate('created_at', '>=', $tgl_1);
        }

        if (!is_null($tgl_2)) {
            $query->whereDate('created_at', '<=', $tgl_2);
        }

        $all_data = $query->get();


        return view('admin.dashboard.index', compact(
            'toptitle',
            'title',
            'subtitle',
            'jumlah_pengguna',
            'jumlah_pengunjung',
            'jumlah_jenis_pengajuan',
            'jumlah_pengajuan',
            'jumlah_pengajuan_selesai',
            'jumlah_pengajuan_user',
            'jenis_pengujian',
            'jenis_layanan',
            'status_transaksi',
            'tgl_1',
            'tgl_2',
            'all_data'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
