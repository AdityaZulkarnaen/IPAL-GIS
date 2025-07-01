<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Providers\RouteServiceProvider;

class PhoneLoginController extends Controller
{
    /**
     * Send OTP to phone number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'nomor_hp' => 'required|string|regex:/^628[0-9]{8,11}$/',
        ], [
            'nomor_hp.required' => 'Nomor HP wajib diisi',
            'nomor_hp.regex' => 'Format nomor HP tidak valid. Contoh: 6281234567890'
        ]);

        $nomor_hp = $request->nomor_hp;

        // Check if user exists (handle multiple database formats)
        $user = $this->findUserByPhone($nomor_hp);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP tidak terdaftar. Silakan daftar terlebih dahulu.'
            ], 422);
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $expiry = now()->addMinutes(5)->timestamp;

        // Store OTP in session temporarily (use original input format as key)
        Session::put('login_otp_' . $nomor_hp, [
            'code' => $otp,
            'expiry' => $expiry,
            'user_id' => $user->id
        ]);

        // Format nomor HP untuk WhatsApp (input sudah format 628xxx)
        $formatted_number = $nomor_hp; // 628155228522 format

        // Send OTP via WhatsApp
        try {
            $response = Http::post('https://wagw.madanateknologi.com/send-message', [
                'api_key' => 'pvFiN1pGDe9VKeljIJj5VNEJnEoXY3',
                'sender' => '6281226067656',
                'number' => $formatted_number, // 628155228522 format
                'message' => 'Kode OTP login SIMLAB BPJK: ' . $otp . '. Berlaku 5 menit. Jangan berikan kode ini kepada siapapun!'
            ]);

            // Log response untuk debugging
            \Log::info('WhatsApp API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
                'number' => $formatted_number,
                'otp' => $otp
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke WhatsApp Anda'
            ]);
        } catch (\Exception $e) {
            \Log::error('WhatsApp Send Failed', [
                'error' => $e->getMessage(),
                'number' => $formatted_number,
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Verify OTP and login user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'nomor_hp' => 'required|string',
            'otp_code' => 'required|string|size:6',
        ]);

        $nomor_hp = $request->nomor_hp;
        $otp_code = $request->otp_code;

        // Get stored OTP
        $storedOtpData = Session::get('login_otp_' . $nomor_hp);

        if (!$storedOtpData) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        // Check if OTP expired
        if (now()->timestamp > $storedOtpData['expiry']) {
            Session::forget('login_otp_' . $nomor_hp);
            return back()->withErrors(['otp_code' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang.']);
        }

        // Verify OTP
        if ($otp_code != $storedOtpData['code']) {
            return back()->withErrors(['otp_code' => 'Kode OTP tidak valid.']);
        }

        // Login user
        $user = User::find($storedOtpData['user_id']);
        
        if ($user) {
            Auth::login($user, true); // true for remember me
            Session::forget('login_otp_' . $nomor_hp);
            
            $request->session()->regenerate();
            
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors(['otp_code' => 'Terjadi kesalahan. Silakan coba lagi.']);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'nomor_hp' => 'required|string',
        ]);

        $nomor_hp = $request->nomor_hp;

        // Check if user exists (handle multiple database formats)
        $user = $this->findUserByPhone($nomor_hp);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP tidak terdaftar.'
            ], 422);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $expiry = now()->addMinutes(5)->timestamp;

        // Update OTP in session (use original input format as key)
        Session::put('login_otp_' . $nomor_hp, [
            'code' => $otp,
            'expiry' => $expiry,
            'user_id' => $user->id
        ]);

        // Format nomor HP untuk WhatsApp (input sudah format 628xxx)
        $formatted_number = $nomor_hp; // 628155228522 format

        // Send OTP via WhatsApp
        try {
            Http::post('https://wagw.madanateknologi.com/send-message', [
                'api_key' => 'pvFiN1pGDe9VKeljIJj5VNEJnEoXY3',
                'sender' => '6281226067656',
                'number' => $formatted_number, // 628155228522 format
                'message' => 'Kode OTP login SIMLAB BPJK: ' . $otp . '. Berlaku 5 menit. Jangan berikan kode ini kepada siapapun!'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP baru telah dikirim ke WhatsApp Anda'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Format phone number from 08xxx to +628xxx for WhatsApp
     */
    private function formatPhoneNumber($nomor_hp)
    {
        // Remove any non-numeric characters first
        $clean_number = preg_replace('/[^0-9]/', '', $nomor_hp);
        
        // If starts with 08, replace with +628
        if (substr($clean_number, 0, 2) === '08') {
            return '+628' . substr($clean_number, 2);
        }
        
        // If starts with 8, add +62
        if (substr($clean_number, 0, 1) === '8') {
            return '+62' . $clean_number;
        }
        
        // If already starts with 62, add +
        if (substr($clean_number, 0, 2) === '62') {
            return '+' . $clean_number;
        }
        
        // If already starts with +62, return as is
        if (substr($nomor_hp, 0, 3) === '+62') {
            return $nomor_hp;
        }
        
        // Default: assume it's Indonesian number starting with 8
        return '+62' . $clean_number;
    }

    /**
     * Find user by phone number (handle multiple database formats)
     */
    private function findUserByPhone($input_phone)
    {
        // Extract number part (remove 62 prefix from input)
        $search_number = substr($input_phone, 2); // 628xxx -> 8xxx
        
        // Log untuk debugging
        \Log::info('Phone Login Search', [
            'input_phone' => $input_phone,
            'search_number' => $search_number,
        ]);
        
        // Search user with multiple format possibilities in database
        $user = User::where(function($query) use ($search_number, $input_phone) {
            $query->where('nomor_hp', '0' . $search_number)     // 08xxx format
                  ->orWhere('nomor_hp', $input_phone)           // 628xxx format (exact match)
                  ->orWhere('nomor_hp', '+' . $input_phone)     // +628xxx format
                  ->orWhere('nomor_hp', '62' . $search_number); // 628xxx format (alternative)
        })->first();
        
        // Log hasil pencarian
        \Log::info('User Found', [
            'user_found' => $user ? true : false,
            'user_id' => $user ? $user->id : null,
            'user_phone' => $user ? $user->nomor_hp : null,
        ]);
        
        return $user;
    }
}
