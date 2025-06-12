<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Swift_TransportException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

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

        $newCode = rand(100000, 999999);
        $newExpiry = now()->addHour()->timestamp;

        $data_user = User::create([
            'name' => $request->name,
            'nomor_hp' => $request->nomor_hp,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'role' => 'Pengguna',
            'verif_wa' => $newCode . '_' . $newExpiry,
            'api_token' => Hash::make($request->nomor_hp . $request->email),
            'password' => Hash::make($request->password),
        ]);

        Http::post('https://wagw.madanateknologi.com/send-message', [
            'api_key' => 'pvFiN1pGDe9VKeljIJj5VNEJnEoXY3',
            'sender' => '6281226067656',
            'number' => $request->nomor_hp,
            'message' => 'Kode verifikasi WhatsApp Anda: ' . $newCode
        ]);

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
            return redirect()->route('register')->with(['success' => 'Email verifikasi berhasil terkirim']);
        } catch (Swift_TransportException $e) {
            return redirect()->route('register')->with(['error' => 'Email verifikasi gagal terkirim : ' . $e->getMessage()]);
        }
    }
}
