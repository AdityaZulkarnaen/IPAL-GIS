<?php

namespace App\Http\Controllers\Pengguna;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class WaGatewayController extends Controller
{
    public function verify_wa(Request $request)
    {
        $user = auth()->user();
        $currentTime = Carbon::now();

        $otpData = explode('_', $user->verif_wa);
        $storedCode = $otpData[0];
        $expiryTime = isset($otpData[1]) ? Carbon::createFromTimestamp($otpData[1]) : null;

        if ($expiryTime && $currentTime->gt($expiryTime)) {
            $newCode = rand(100000, 999999);
            $newExpiry = $currentTime->addHour()->timestamp;

            $user->update([
                'verif_wa' => $newCode . '_' . $newExpiry
            ]);

            Http::post('https://wagw.madanateknologi.com/send-message', [
                'api_key' => 'pvFiN1pGDe9VKeljIJj5VNEJnEoXY3',
                'sender' => '6281226067656',
                'number' => $user->nomor_hp,
                'message' => 'Kode verifikasi baru Anda: ' . $newCode
            ]);

            return back()->with('error', 'Kode OTP telah kedaluwarsa. Kami telah mengirim kode baru ke WhatsApp Anda.');
        }

        if ($request->wa_otp == $storedCode) {
            $user->update([
                'verif_wa' => 'aktif',
                'email_verified_at' => $currentTime
            ]);

            return redirect()->route('dashboard.index')->with('success', 'Verifikasi berhasil!');
        }

        return back()->with('error', 'Kode OTP tidak valid');
    }

    public function resend_wa(Request $request)
    {
        $user = auth()->user();
        $newCode = rand(100000, 999999);
        $newExpiry = now()->addHour()->timestamp;

        $user->update([
            'verif_wa' => $newCode . '_' . $newExpiry
        ]);

        Http::post('https://wagw.madanateknologi.com/send-message', [
            'api_key' => 'pvFiN1pGDe9VKeljIJj5VNEJnEoXY3',
            'sender' => '6281226067656',
            'number' => $user->nomor_hp,
            'message' => 'Kode verifikasi WhatsApp Anda: ' . $newCode
        ]);

        return back()->with('success', 'Kode verifikasi baru telah dikirim ke WhatsApp Anda.');
    }
}
