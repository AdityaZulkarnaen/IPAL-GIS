<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisModel;
use Illuminate\Http\Request;
use App\Models\TransaksiProdukModel;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $toptitle = 'Fitur';
        $title = 'Pengajuan';
        $subtitle = 'Tambah Produk';

        $data_jenis_pengujian = JenisModel::with('parameter_uji')
            ->get();

        return view('admin.pembayaran.create-produk', compact(
            'toptitle',
            'title',
            'subtitle',
            'data_jenis_pengujian',
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'required',
            'nama' => 'required',
        ]);


        $dataUp['nama'] = $request->nama;
        $dataUp['id_parameter_uji'] = $request->id_param_uji;
        $dataUp['no_order'] = $request->no_order;
        $dataUp['kode_sampel'] = $request->kode_sampel;
        $dataUp['jumlah'] = $request->jumlah;

        $data_input = TransaksiProdukModel::create([
            'id_transaksi' => $request->id_transaksi,
            'nama' => $request->nama,
            'id_parameter_uji' => $request->id_param_uji,
            'no_order' => $request->no_order,
            'kode_sampel' => $request->kode_sampel,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('pengajuan_uji.edit', $request->id_transaksi)->with(['success' => 'Data Berhasil Disimpan']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $toptitle = 'Fitur';
        $title = 'Pengajuan';
        $subtitle = 'Tambah Produk';

        $id_transaksi = $id;
        $data_jenis_pengujian = JenisModel::with('parameter_uji')
            ->get();

        return view('admin.pembayaran.create-produk', compact(
            'toptitle',
            'title',
            'subtitle',
            'id_transaksi',
            'data_jenis_pengujian',
        ));
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
        $subtitle = 'Edit Produk';

        $data_jenis_pengujian = JenisModel::with('parameter_uji')
            ->get();
        $data_edit = TransaksiProdukModel::with('parameter_uji.jenis')->where('id', $id)->first();

        return view('admin.pembayaran.edit-produk', compact(
            'toptitle',
            'title',
            'subtitle',
            'data_jenis_pengujian',
            'data_edit'
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
            'nama' => 'required',
        ]);

        $data_up = TransaksiProdukModel::where('id', $id)->first();
        $dataUp['nama'] = $request->nama;
        $dataUp['id_parameter_uji'] = $request->id_param_uji;
        $dataUp['no_order'] = $request->no_order;
        $dataUp['kode_sampel'] = $request->kode_sampel;
        $dataUp['jumlah'] = $request->jumlah;

        $data_up->update($dataUp);
        return redirect()->route('pengajuan_uji.edit', $data_up->id_transaksi)->with(['success' => 'Data Berhasil Disimpan']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data_del = TransaksiProdukModel::findOrFail($id);
        $id_red = $data_del->id_transaksi;
        $data_del->delete();
        return redirect()->route('pengajuan_uji.edit', $id_red)->with(['success' => 'Data Berhasil Dihapus']);
    }
}
