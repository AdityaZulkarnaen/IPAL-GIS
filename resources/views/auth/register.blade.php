<x-guest-layout>
    @if(Session::has('success'))
    <div style="color:green; text-align:center;">
        <small>
            <strong>Berhasil! </strong> {{ Session('success') }}
        </small>
    </div>
    <br>
    @endif

    @if ($errors->any())
    <div style="color:red; text-align:center;">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <br>
    @endif
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- No. HP -->
        <div class="mt-4">
            <x-input-label for="nomor_hp" :value="__('No. HP (Contoh: +6281122223333)')" />
            <x-text-input id="nomor_hp" class="block mt-1 w-full" type="number" name="nomor_hp" :value="old('nomor_hp')" required autofocus autocomplete="nomor_hp" />
            <x-input-error :messages="$errors->get('nomor_hp')" class="mt-2" />
            <small id="nomor_hp_error" class="text-light"></small>
            <script>
                document.getElementById('nomor_hp').addEventListener('input', function() {
                    var nomor_hp = this.value;
            
                    if (nomor_hp.length < 10) {
                        document.getElementById('nomor_hp_error').innerText = '*Nomor HP harus memiliki minimal 10 karakter.';
                    } else {
                        document.getElementById('nomor_hp_error').innerText = '';
                    }
                });
            </script>
        </div>

        <!-- Alamat -->
        <div class="mt-4">
            <x-input-label for="alamat" :value="__('Alamat')" />
            <x-text-input id="alamat" class="block mt-1 w-full" type="text" name="alamat" :value="old('alamat')" required autofocus autocomplete="alamat" />
            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Ulangi Kata Sandi')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah punya akun? Login disini!') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Mendaftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>