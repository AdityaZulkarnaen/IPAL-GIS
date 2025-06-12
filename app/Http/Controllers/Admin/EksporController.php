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
use Carbon\Carbon;

class EksporController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toptitle = 'Ekspor';
        $title = 'Ekspor';
        $subtitle = 'Data Ekspor';

        $sekarang = Carbon::now();
        

        $jenis_layanan = $request->jenis_layanan;
        $status_transaksi = $request->status_transaksi;
        $tgl_1 = $request->tgl_1;
        $tgl_2 = $request->tgl_2;
        $kata_kunci = $request->kata_kunci;

        $query = TransaksiModel::with('user')
            ->orderBy('created_at', 'DESC');
        
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
        
        if (!is_null($kata_kunci)) {
            $query->where(function ($q) use ($kata_kunci) {
                $q->whereHas('user', function ($subq) use ($kata_kunci) {
                    $subq->where('name', 'like', '%' . $kata_kunci . '%');
                })->orWhere('kode_sampel', 'like', '%' . $kata_kunci . '%');
            });
        }
        
        $all_data = $query->get();
        // $all_data->appends([
        //     'jenis_layanan' => $jenis_layanan,
        //     'status_transaksi' => $status_transaksi,
        //     'tgl_1' => $tgl_1,
        //     'tgl_2' => $tgl_2,
        //     'kata_kunci' => $kata_kunci
        // ]);


        return view('admin.dashboard.ekspor', compact(
            'toptitle',
            'title',
            'subtitle',
            'jenis_layanan',
            'status_transaksi',
            'tgl_1',
            'tgl_2',
            'all_data',
            'kata_kunci'
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
