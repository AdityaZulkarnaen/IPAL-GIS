<x-guest-layout>
    {{-- Success Notification --}}
    @if(Session::has('success'))
    <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 border border-green-300 rounded-md">
        {{ session('success') }}
    </div>
    @endif

    {{-- Verification Link Sent Notification --}}
    @if (session('status') == 'verification-link-sent')
    <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 border border-green-300 rounded-md">
        {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
    </div>
    @endif

    {{-- Introduction --}}
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Verifikasi Akun Anda</h2>
        <p class="text-gray-600">
            {{ __('Sebelum memulai, verifikasi alamat email Anda dengan mengeklik tautan yang kami kirimkan melalui email. Jika Anda tidak menerima email tersebut, kami dapat mengirim ulang.') }}
        </p>
    </div>

    {{-- WA Verification Form --}}
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-700 mb-3">Verifikasi via WhatsApp</h3>
        <form method="POST" action="{{ route('verify.wa') }}" class="space-y-3">
            @csrf
            <div class="flex flex-col sm:flex-row gap-3">
                <x-text-input
                    id="wa_otp"
                    name="wa_otp"
                    type="text"
                    required
                    placeholder="Masukkan Kode WA"
                    class="flex-1" />
                <x-primary-button type="submit" class="w-full sm:w-auto justify-center">
                    {{ __('Verifikasi') }}
                </x-primary-button>
            </div>
            @error('wa_otp')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </form>

        {{-- Resend WA Code Button --}}
        <form method="POST" action="{{ route('resend.wa') }}" class="mt-2">
            @csrf
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                {{ __('Kirim Ulang Kode WA') }}
            </button>
        </form>
    </div>

    <div class="border-t border-gray-200 my-6"></div>

    {{-- Email Verification Section --}}
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-700 mb-3">Verifikasi via Email</h3>
        <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
            @csrf
            <x-primary-button class="w-full justify-center">
                {{ __('Kirim Ulang Kode ke Email') }}
            </x-primary-button>
        </form>
    </div>

    {{-- Logout --}}
    <div class="text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 hover:text-gray-800 hover:underline">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>