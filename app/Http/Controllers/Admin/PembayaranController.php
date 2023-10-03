<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisModel;
use App\Models\StatusTransaksiProdukModel;
use App\Models\TransaksiModel;
use App\Models\TransaksiProdukModel;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toptitle = 'Fitur';
        $title = 'Pengajuan';
        $subtitle = 'Data Pengajuan';

        $jenis_layanan = $request->jenis_layanan;
        $status_transaksi = $request->status_transaksi;
        $tgl_1 = $request->tgl_1;
        $tgl_2 = $request->tgl_2;

        $jenis_pengujian = JenisModel::orderBy('id', 'DESC')
            ->get();

        $query = TransaksiModel::with('user')
            ->orderBy('id', 'DESC');

        // $query = TransaksiModel::with('user')->leftJoin('transaksi_produk', 'transaksi.id', '=', 'transaksi_produk.id_transaksi')->orderBy('transaksi.id', 'DESC');

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

        return view('admin.pembayaran.index', compact(
            'toptitle',
            'title',
            'subtitle',
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
        $data_edit_prod = TransaksiProdukModel::where('id', $id)->first();
        return view('admin.pembayaran.edit', compact('data_edit_prod'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $toptitle = 'Fitur';
        $title = 'Pengajuan';
        $subtitle = 'Tindak Lanjut Pengajuan';

        $data_edit = TransaksiModel::with('status')->where('id', $id)->first();

        // $data_edit = TransaksiModel::with('user')->rightJoin('transaksi_produk', 'transaksi.id', '=', 'transaksi_produk.id_transaksi')->select('transaksi_produk.id as id', 'transaksi.id_user as id_user', 'transaksi.kegiatan as kegiatan', 'transaksi.no_dokumen as no_dokumen', 'transaksi_produk.created_at as created_at', 'transaksi.sumber as sumber', 'transaksi_produk.status_bayar as status_bayar', 'transaksi_produk.no_order as no_order', 'transaksi_produk.kode_sampel as kode_sampel', 'transaksi_produk.catatan as catatan')->where('transaksi_produk.id', $id)->first();

        $data_edit_prod = TransaksiProdukModel::where('id', $id)->get();

        $data_jenis_pengujian = JenisModel::with('parameter_uji')
            ->get();

        $data_transaksi_produk = TransaksiProdukModel::with('parameter_uji')
            ->where('id_transaksi', $id)
            ->orderBy('id', 'DESC')
            ->get();

        // dd($data_transaksi_produk);

        return view('admin.pembayaran.edit', compact(
            'toptitle',
            'title',
            'subtitle',
            'data_edit',
            'data_transaksi_produk',
            'data_jenis_pengujian',
            'data_edit_prod'
        ));
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
        $request->validate([
            'status_bayar' => 'required',
        ]);

        // $data_up = TransaksiModel::findOrFail($id);

        // $data_up = TransaksiModel::with('user')->where('id', $id);
        $data_up = TransaksiModel::where('id', $id);

        // $data_up = TransaksiModel::with('user')->rightJoin('transaksi_produk', 'transaksi.id', '=', 'transaksi_produk.id_transaksi')->select('transaksi_produk.id as id', 'transaksi.id_user as id_user', 'transaksi.kegiatan as kegiatan', 'transaksi.no_dokumen as no_dokumen', 'transaksi_produk.created_at as created_at', 'transaksi.sumber as sumber', 'transaksi_produk.status_bayar as status_bayar', 'transaksi_produk.no_order as no_order', 'transaksi_produk.kode_sampel as kode_sampel', 'transaksi_produk.catatan as catatan')->where('transaksi_produk.id', $id);

        $dataUp['no_order'] = $request->no_order;
        $dataUp['kode_sampel'] = $request->kode_sampel;
        $dataUp['status_bayar'] = $request->status_bayar;
        $dataUp['catatan'] = $request->catatan;
        $data_up->update($dataUp);

        $berkas = "";
        if ($request->hasFile('berkas')) {
            $file = $request->file('berkas');
            $fileName = auth()->user()->id . time() . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path() . '/berkas';
            $file->move($destinationPath, $fileName);
            $berkas = 'berkas/' . $fileName;
        }

        $data_input = StatusTransaksiProdukModel::create([
            'id_status' => $id,
            'id_transaksi_produk' => $request->id_transaksi_produk,
            'catatan' => $request->status_bayar . ', ' . $request->catatan,
            'berkas' => $berkas,
        ]);

        return redirect()->route('dashboard.index')->with(['success' => 'Data Berhasil Disimpan']);

        // return back()->with(['success' => 'Data Berhasil Disimpan']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data_del = TransaksiModel::find($id);
        $data_del->delete();
        return redirect()->route('dashboard.index')->with(['success' => 'Data Berhasil Dihapus']);
    }
}
