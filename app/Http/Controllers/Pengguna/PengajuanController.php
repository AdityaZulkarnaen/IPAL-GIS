<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\JenisModel;
use App\Models\ParameterUjiModel;
use App\Models\TransaksiModel;
use App\Models\TransaksiProdukModel;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;


class PengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $toptitle = 'Fitur';
        $title = 'Pengajuan';
        $subtitle = 'Data Pengajuan';

        if (auth()->user() == null) {
            return redirect()->route('login');
        }

        $all_data = TransaksiModel::with('status')
            ->where('id_user', auth()->user()->id)
            ->orderBy('id', 'DESC')
            ->get();

        // $all_data = TransaksiModel::where('id_user', auth()->user()->id)
        // ->rightJoin('transaksi_produk', 'transaksi.id', '=', 'transaksi_produk.id_transaksi')
        // ->select('transaksi_produk.id as id', 'transaksi.id_user as id_user', 'transaksi.kegiatan as kegiatan', 'transaksi.no_dokumen as no_dokumen', 'transaksi_produk.created_at as created_at', 'transaksi.sumber as sumber', 'transaksi_produk.status_bayar as status_bayar', 'transaksi_produk.no_order as no_order', 'transaksi_produk.kode_sampel as kode_sampel', 'transaksi_produk.nama as nama', 'transaksi_produk.catatan as catatan')
        // ->orderBy('transaksi_produk.created_at', 'DESC')->get();

        return view('pengguna.pengajuan.index', compact(
            'toptitle',
            'title',
            'subtitle',
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
        $toptitle = 'Fitur';
        $title = 'Pengajuan';
        $subtitle = 'Tambah Pengajuan';

        $data_jenis_pengujian = JenisModel::with('parameter_uji')
            ->get();

        return view('pengguna.pengajuan.create', compact(
            'toptitle',
            'title',
            'subtitle',
            'data_jenis_pengujian'
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
            'kegiatan' => 'required',
            'sumber' => 'required',
            'id_jenis' => 'required',
            // 'jumlah[]' => 'required|gt:0',
        ]);

        $data_user = User::where('email', $request->email)->first();
        $pesan = "";

        if ($data_user == null) {
            
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);
        
            // Tambahkan aturan validasi untuk memeriksa domain email
            $validator->after(function ($validator) use ($request) {
                $allowedDomains = ['gmail.com', 'yahoo.co.id', 'yahoo.com'];
                $emailDomain = explode('@', $request->email)[1];
        
                if (!in_array($emailDomain, $allowedDomains)) {
                    $validator->errors()->add('email', 'Hanya alamat email dari Gmail (.com) atau Yahoo (.com, .co.id) yang diperbolehkan.');
                }
            });
        
            if ($validator->fails()) {
                echo $validator->errors()->first();
                die();
            }
            
            $email = $request->email;
        
            $data_user = [
                'name' => $request->name,
                'nomor_hp' => $request->nomor_hp,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'role' => 'Pengguna',
                'api_token' => Hash::make($request->nomor_hp . $request->email),
                'password' => Hash::make($request->password),
            ];
            
            $data_input = [
                'id_jenis' => $request->id_jenis[0],
                'kegiatan' => $request->kegiatan,
                'no_dokumen' => $request->no_dokumen,
                'tanggal' => $request->tanggal,
                'revisi' => $request->revisi,
                'sumber' => $request->sumber,
                'status_bayar' => 'Belum Dibayar'

            ];

            $data_input_param_uji = [];

            foreach ($request->nama_produk as $index => $namaProduk) {
                if ($request->id_param_uji[$index] > 0) {
                    $data_input_param_uji[] = [
                        'id_parameter_uji' => $request->id_param_uji[$index],
                        'nama' => $namaProduk,
                        'jumlah' => $request->jumlah_sampel[$index],
                    ];
                }
            }
    
            $verificationToken = preg_replace('/[^A-Za-z]/', '', bcrypt($email));
    
            cache()->put('user_data_' . $email, $data_user, now()->addMinutes(60));
            cache()->put('data_input_' . $email, $data_input, now()->addMinutes(60));
            cache()->put('data_input_param_uji_' . $email, $data_input_param_uji, now()->addMinutes(60));
            cache()->put('email_verification_' . $email, $verificationToken, now()->addMinutes(60));
    
            $verificationLink = route('verify_input_pengajuan.email', ['email' => $email, 'token' => $verificationToken]);
    
            $pesan = "Klik link berikut untuk verifikasi email Anda: <a href='{$verificationLink}'>Verifikasi Email</a>";
    
            try {
                // OTP WhatsApp mirip fitur register
                $newCode = rand(100000, 999999);
                $newExpiry = now()->addHour()->timestamp;
                // Simpan kode OTP dan expiry ke cache (atau bisa ke DB jika ingin)
                cache()->put('otp_wa_' . $email, [
                    'code' => $newCode,
                    'expiry' => $newExpiry,
                    'nomor_hp' => $request->nomor_hp
                ], now()->addHour());

                // Kirim OTP ke WhatsApp
                \Illuminate\Support\Facades\Http::post('https://wagw.madanateknologi.com/send-message', [
                    'api_key' => 'pvFiN1pGDe9VKeljIJj5VNEJnEoXY3',
                    'sender' => '6281226067656',
                    'number' => $request->nomor_hp,
                    'message' => 'Kode verifikasi WhatsApp Anda: ' . $newCode
                ]);

                Mail::raw($pesan, function ($message) use ($email, $pesan) {
                    $message->to($email)
                        ->subject("Verifikasi Email!")
                        ->html($pesan);
                });
                // echo 'Email berhasil terkirim';
                // die();
                // Setelah OTP dikirim, redirect ke halaman form OTP pengajuan
                return redirect()->route('verifikasi.pengajuan', ['email' => $email])->with(['success' => 'Kode verifikasi berhasil dikirim ke WhatsApp & Email. Silakan cek dan verifikasi akun Anda!']);
            } catch (\Exception $e) {
                return redirect()->route('register')->with(['error' => 'Email verifikasi gagal terkirim : ' . $e->getMessage()]);
                // echo 'Email gagal terkirim: ' . ;
                // die();
            }
                
            // $user = User::create([
            //     'name' => $request->name,
            //     'nomor_hp' => $request->nomor_hp,
            //     'alamat' => $request->alamat,
            //     'email' => $request->email,
            //     'role' => 'Pengguna',
            //     'api_token' => Hash::make($request->nomor_hp . $request->email),
            //     'password' => Hash::make($request->email)
            // ]);

            // $id_user = $user->id;
            

            // $data_input = TransaksiModel::create([
            //     'id_user' => $id_user,
            //     'id_jenis' => $request->id_jenis[0],
            //     'kegiatan' => $request->kegiatan,
            //     'no_dokumen' => $request->no_dokumen,
            //     'tanggal' => $request->tanggal,
            //     'revisi' => $request->revisi,
            //     'sumber' => $request->sumber,
            //     'status_bayar' => 'Belum Dibayar'

            // ]);

            // foreach ($request->nama_produk as $index => $namaProduk) {
            //     if ($request->id_param_uji[$index] > 0) {
            //         $data_input_param_uji = TransaksiProdukModel::create([
            //             'id_transaksi' => $data_input->id,
            //             'id_parameter_uji' => $request->id_param_uji[$index],
            //             'nama' => $namaProduk,
            //             'jumlah' => $request->jumlah_sampel[$index],
            //         ]);

            //         // $dataUp['kode_sampel'] = $data_input_param_uji->id . '/BB/' . date('Y', time());
            //         // $data_input_param_uji->update($dataUp);
            //     }
            // }

            // event(new Registered($user));

            // // Auth::login($user);

            // $pesan = "Akun telah dibuat, login dengan email " . $request->email . " dengan kata sandi " . $request->email;
            // return redirect(RouteServiceProvider::HOME)->with(['success' => $pesan]);

            // return redirect()->route('cetak_permintaan_pengujian.show', $data_input->id)->with(['success' => $pesan]);
        } else {
            $id_user = $data_user->id;
        }

        $data_input = TransaksiModel::create([
            'id_user' => $id_user,
            'id_jenis' => $request->id_jenis[0],
            'kegiatan' => $request->kegiatan,
            'no_dokumen' => $request->no_dokumen,
            'tanggal' => $request->tanggal,
            'revisi' => $request->revisi,
            'sumber' => $request->sumber,
            'status_bayar' => 'Belum Dibayar'
        ]);

        foreach ($request->nama_produk as $index => $namaProduk) {
            if ($request->id_param_uji[$index] > 0) {
                $data_input_param_uji = TransaksiProdukModel::create([
                    'id_transaksi' => $data_input->id,
                    'id_parameter_uji' => $request->id_param_uji[$index],
                    'nama' => $namaProduk,
                    'jumlah' => $request->jumlah_sampel[$index],
                ]);

                // $dataUp['kode_sampel'] = $data_input_param_uji->id . '/BB/' . date('Y', time());
                // $data_input_param_uji->update($dataUp);
            }
        }

        $pesan = $pesan . "Pengajuan telah dibuat.";

        return redirect()->route('pengajuan.index')->with(['success' => $pesan]);

        // return back()->with('message', 'Pesanan Sudah Terkirim');
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
        $subtitle = 'Detail Pengajuan';
    
        $cek_data_user = TransaksiModel::where('id', $id)
            ->where('id_user', auth()->user()->id)
            ->first();
    
        if ($cek_data_user == null) {
            return redirect()->route('pengajuan.index')->with('error', 'Data pengajuan tidak ditemukan atau Anda tidak memiliki akses ke data ini.');
        }
    
        $all_data = TransaksiProdukModel::with('parameter_uji')
            ->where('id_transaksi', $id)
            ->orderBy('id', 'DESC')
            ->get();
    
        return view('pengguna.pengajuan.detail', compact(
            'toptitle',
            'title',
            'subtitle',
            'all_data'
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
        $subtitle = 'Edit Pengajuan';

        $data_edit = TransaksiProdukModel::where('id', $id)->get();
        $data_jenis = JenisModel::all();

        return view('pengguna.pengajuan.edit', compact(
            'toptitle',
            'title',
            'subtitle',
            'data_edit',
            'data_jenis'
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
            'kegiatan' => 'required',
            'sumber' => 'required',
            'id_jenis' => 'required',
        ]);

        $data_up = TransaksiProdukModel::findOrFail($id);

        $dataUp['kegiatan'] = $request->kegiatan;
        $dataUp['sumber'] = $request->sumber;
        $dataUp['id_jenis'] = $request->id_jenis;
        $dataUp['no_dokumen'] = $request->no_dokumen;


        $data_up = TransaksiModel::findOrFail($id);
        $data_up->update($dataUp);
        return redirect()->route('pengajuan.index')->with(['success' => 'Data Berhasil Disimpan']);
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
        return redirect()->route('pengajuan.index')->with(['success' => 'Data Berhasil Dihapus']);
    }
}
