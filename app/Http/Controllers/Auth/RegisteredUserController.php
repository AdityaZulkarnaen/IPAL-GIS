<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $newCode = rand(100000, 999999);
        $newExpiry = now()->addHour()->timestamp;

        $user = User::create([
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

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
