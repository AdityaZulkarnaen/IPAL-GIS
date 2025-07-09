<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Swift_TransportException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Models\TransaksiModel;
use App\Models\TransaksiProdukModel;

class VerifEmailController extends Controller
{
    public function send(Request $request)
    {
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

        $verificationToken = preg_replace('/[^A-Za-z]/', '', bcrypt($email));

        cache()->put('user_data_' . $email, $data_user, now()->addMinutes(60));
        cache()->put('email_verification_' . $email, $verificationToken, now()->addMinutes(60));

        $verificationLink = route('verify.email', ['email' => $email, 'token' => $verificationToken]);

        $pesan = "Klik link berikut untuk verifikasi email Anda: <a href='{$verificationLink}'>Verifikasi Email</a>";

        try {
            Mail::raw($pesan, function ($message) use ($email, $pesan) {
                $message->to($email)
                    ->subject("Verifikasi Email!")
                    ->html($pesan);
            });
            return response()->json(['status' => 'success', 'message' => 'Email berhasil terkirim']);
        } catch (Swift_TransportException $e) {
            return response()->json(['status' => 'error', 'message' => 'Email gagal terkirim: ' . $e->getMessage()]);
        }
    }

    public function verifyEmail($email, $token)
    {
        $storedToken = cache()->get('email_verification_' . $email);
        $userData = cache()->get('user_data_' . $email);

        if ($token === $storedToken) {

            $user = User::create([
                'name' => $userData['name'],
                'nomor_hp' => $userData['nomor_hp'],
                'alamat' => $userData['alamat'],
                'email' => $userData['email'],
                'role' => 'Pengguna',
                'email_verified_at' => now(),
                'api_token' => Hash::make($userData['nomor_hp'] . $userData['email']),
                'password' => Hash::make($userData['password']),
            ]);

            cache()->forget('email_verification_' . $email);
            cache()->forget('user_data_' . $email);

            // event(new Registered($user));

            Auth::login($user);

            return redirect(RouteServiceProvider::HOME);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Token verifikasi tidak valid']);
        }
    }

    public function verifyEmailDanInput($email, $token)
    {
        $storedToken = cache()->get('email_verification_' . $email);
        $userData = cache()->get('user_data_' . $email);
        $inputData = cache()->get('data_input_' . $email);
        $inputParamUjiData = cache()->get('data_input_param_uji_' . $email);

        if ($token === $storedToken) {

            $user = User::create([
                'name' => $userData['name'],
                'nomor_hp' => $userData['nomor_hp'],
                'alamat' => $userData['alamat'],
                'email' => $userData['email'],
                'role' => 'Pengguna',
                'email_verified_at' => now(),
                'api_token' => Hash::make($userData['nomor_hp'] . $userData['email']),
                'password' => Hash::make($userData['password']),
            ]);

            $id_user = $user->id;

            $data_input = TransaksiModel::create([
                'id_user' => $id_user,
                'id_jenis' => $inputData['id_jenis'],
                'kegiatan' => $inputData['kegiatan'],
                'no_dokumen' => $inputData['no_dokumen'],
                'tanggal' => $inputData['tanggal'],
                'revisi' => $inputData['revisi'],
                'sumber' => $inputData['sumber'],
                'status_bayar' => 'Belum Dibayar'

            ]);

            foreach ($inputParamUjiData as $dtParam) {
                $data_input_param_uji = TransaksiProdukModel::create([
                    'id_transaksi' => $data_input->id,
                    'id_parameter_uji' => $dtParam['id_parameter_uji'],
                    'nama' => $dtParam['nama'],
                    'jumlah' => $dtParam['jumlah'],
                ]);
            }

            cache()->forget('email_verification_' . $email);
            cache()->forget('user_data_' . $email);
            cache()->forget('data_input_' . $email);
            cache()->forget('data_input_param_uji_' . $email);

            // event(new Registered($user));

            Auth::login($user);

            return redirect(RouteServiceProvider::HOME);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Token verifikasi tidak valid']);
        }
    }

    // Verifikasi OTP untuk pengajuan (dari form wa_otp_pengajuan)
    public function verifyOtpPengajuan(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'wa_otp_pengajuan' => 'required|digits:6',
        ]);
        $email = $request->email;
        $otp = $request->wa_otp_pengajuan;
        $otpData = cache()->get('otp_wa_' . $email);
        if (!$otpData || $otpData['code'] != $otp || $otpData['expiry'] < now()->timestamp) {
            return back()->with('error', 'Kode OTP salah atau sudah kadaluarsa.');
        }
        // Ambil data dari cache
        $userData = cache()->get('user_data_' . $email);
        $inputData = cache()->get('data_input_' . $email);
        $inputParamUjiData = cache()->get('data_input_param_uji_' . $email);
        if (!$userData || !$inputData || !$inputParamUjiData) {
            return redirect()->route('pengajuan.create')->with('error', 'Data tidak ditemukan, silakan ulangi proses.');
        }
        // Simpan user
        $user = User::create([
            'name' => $userData['name'],
            'nomor_hp' => $userData['nomor_hp'],
            'alamat' => $userData['alamat'],
            'email' => $userData['email'],
            'role' => 'Pengguna',
            'email_verified_at' => now(),
            'api_token' => \Illuminate\Support\Facades\Hash::make($userData['nomor_hp'] . $userData['email']),
            'password' => $userData['password'],
        ]);
        $id_user = $user->id;
        $data_input = TransaksiModel::create([
            'id_user' => $id_user,
            'id_jenis' => $inputData['id_jenis'],
            'kegiatan' => $inputData['kegiatan'],
            'no_dokumen' => $inputData['no_dokumen'],
            'tanggal' => $inputData['tanggal'],
            'revisi' => $inputData['revisi'],
            'sumber' => $inputData['sumber'],
            'status_bayar' => 'Belum Dibayar'
        ]);
        foreach ($inputParamUjiData as $dtParam) {
            TransaksiProdukModel::create([
                'id_transaksi' => $data_input->id,
                'id_parameter_uji' => $dtParam['id_parameter_uji'],
                'nama' => $dtParam['nama'],
                'jumlah' => $dtParam['jumlah'],
            ]);
        }
        // Bersihkan cache
        cache()->forget('otp_wa_' . $email);
        cache()->forget('user_data_' . $email);
        cache()->forget('data_input_' . $email);
        cache()->forget('data_input_param_uji_' . $email);
        cache()->forget('email_verification_' . $email);
        // Login user
        Auth::login($user);
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dibuat dan akun berhasil diverifikasi!');
    }

    // Tampilkan view verif-email.blade untuk pengajuan (form OTP dan email)
    public function showVerifEmailPengajuan(Request $request)
    {
        $email = $request->query('email');
        return view('auth.verify-email', compact('email'));
    }
}
