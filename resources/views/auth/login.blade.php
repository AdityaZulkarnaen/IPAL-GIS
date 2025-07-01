<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Login Method Toggle -->
    <div class="mb-6">
        <div class="flex border-b border-gray-200">
            <button type="button" id="phone-tab" class="px-4 py-2 text-sm font-medium text-gray-700 border-b-2 border-indigo-500 bg-white">
                {{ __('Login dengan Whatsapp') }}
            </button>
            <button type="button" id="email-tab" class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent bg-white hover:text-gray-700 hover:border-gray-300">
                {{ __('Login dengan Email') }}
            </button>
        </div>
    </div>

    <!-- Email Login Form -->
    <form method="POST" action="{{ route('login') }}" id="email-login-form" style="display: none;">
        @csrf
        <input type="hidden" name="login_type" value="email">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata sandi')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                {{ __('Lupa kata sandi?') }}
            </a>
            @endif

            <x-primary-button class="ml-3">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            @if (Route::has('register'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                {{ __('Belum punya akun? Daftar disini!') }}
            </a>
            @endif
        </div>
    </form>
    <!-- Phone Login Form -->
    <form method="POST" action="{{ route('login.phone') }}" id="phone-login-form">
        @csrf
        <input type="hidden" name="login_type" value="phone">

        <!-- Phone Number -->
        <div>
            <x-input-label for="nomor_hp" :value="__('Nomor Whatsapp')" />
            <x-text-input id="nomor_hp" class="block mt-1 w-full" type="tel" name="nomor_hp" :value="old('nomor_hp')" required placeholder="Contoh: 6281234567890" />
            <x-input-error :messages="$errors->get('nomor_hp')" class="mt-2" />
            <small class="text-gray-500">Format otomatis: 62xxxxxxxxxx<br>Pastikan nomor WhatsApp aktif</small>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Kirim Kode OTP') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            @if (Route::has('register'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                {{ __('Belum punya akun? Daftar disini!') }}
            </a>
            @endif
        </div>
    </form>

    <!-- OTP Verification Form (Hidden by default) -->
    <form method="POST" action="{{ route('login.phone.verify') }}" id="otp-verification-form" style="display: none;">
        @csrf
        <input type="hidden" name="nomor_hp" id="otp_nomor_hp" value="">

        <div class="text-center mb-4">
            <h3 class="text-lg font-medium text-gray-700">Verifikasi OTP</h3>
            <p class="text-sm text-gray-600">Kode OTP telah dikirim ke WhatsApp nomor <span id="phone-display"></span></p>
        </div>

        <!-- OTP Input -->
        <div>
            <x-input-label for="otp_code" :value="__('Kode OTP')" />
            <x-text-input id="otp_code" class="block mt-1 w-full text-center text-2xl tracking-widest" type="text" name="otp_code" required maxlength="6" placeholder="000000" />
            <x-input-error :messages="$errors->get('otp_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <button type="button" id="back-to-phone" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('← Kembali') }}
            </button>
            
            <div class="flex space-x-2">
                <button type="button" id="resend-otp" class="text-sm text-blue-600 hover:text-blue-800">
                    {{ __('Kirim Ulang') }}
                </button>
                <x-primary-button>
                    {{ __('Verifikasi & Login') }}
                </x-primary-button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailTab = document.getElementById('email-tab');
            const phoneTab = document.getElementById('phone-tab');
            const emailForm = document.getElementById('email-login-form');
            const phoneForm = document.getElementById('phone-login-form');
            const otpForm = document.getElementById('otp-verification-form');
            const phoneInput = document.getElementById('nomor_hp');

            // Auto-format phone number input dengan format 62 (tanpa +)
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value;
                let cursorPos = e.target.selectionStart;
                
                // Remove all non-numeric characters
                let cleanValue = value.replace(/[^0-9]/g, '');
                
                // Smart auto-format logic
                if (cleanValue === '0') {
                    // User ketik 0, auto ganti ke 62
                    e.target.value = '62';
                    e.target.setSelectionRange(2, 2); // Set cursor setelah 62
                    return;
                } else if (cleanValue === '08') {
                    // User ketik 08, auto ganti ke 628
                    e.target.value = '628';
                    e.target.setSelectionRange(3, 3); // Set cursor setelah 628
                    return;
                } else if (cleanValue.startsWith('08')) {
                    // Convert 08xxx to 628xxx
                    cleanValue = '628' + cleanValue.substring(2);
                } else if (cleanValue.startsWith('8') && !cleanValue.startsWith('628')) {
                    // Convert 8xxx to 628xxx (tapi pastikan bukan yang sudah 628)
                    cleanValue = '628' + cleanValue.substring(1);
                } else if (cleanValue.startsWith('62') && cleanValue.length > 2 && !cleanValue.startsWith('628')) {
                    // Jika user ketik 62 lalu angka lain selain 8, tambahkan 8
                    if (cleanValue.charAt(2) !== '8') {
                        cleanValue = '628' + cleanValue.substring(2);
                    }
                } else if (!cleanValue.startsWith('62') && cleanValue.length > 0 && !cleanValue.startsWith('0')) {
                    // Default: add 62 prefix for other numbers
                    cleanValue = '62' + cleanValue;
                }
                
                // Limit total length (62 + max 11 digits = 13 chars)
                if (cleanValue.length > 13) {
                    cleanValue = cleanValue.substring(0, 13);
                }
                
                // Update input value
                e.target.value = cleanValue;
                
                // Adjust cursor position if needed
                if (cleanValue.startsWith('628') && cursorPos < 3) {
                    e.target.setSelectionRange(3, 3);
                } else if (cleanValue.startsWith('62') && !cleanValue.startsWith('628') && cursorPos < 2) {
                    e.target.setSelectionRange(2, 2);
                }
            });

            // Handle keydown untuk experience yang lebih smooth
            phoneInput.addEventListener('keydown', function(e) {
                // Jika user coba hapus 62, prevent
                if ((e.key === 'Backspace' || e.key === 'Delete') && 
                    e.target.selectionStart <= 2 && e.target.value.startsWith('62')) {
                    e.preventDefault();
                }
            });

            // Prevent non-numeric input
            phoneInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                    e.preventDefault();
                }
            });

            // Handle paste event
            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                let paste = (e.clipboardData || window.clipboardData).getData('text');
                let cleanPaste = paste.replace(/[^0-9]/g, '');
                
                if (cleanPaste.startsWith('0')) {
                    cleanPaste = cleanPaste.substring(1);
                }
                
                // Auto format: jika paste 8xxx, jadikan 628xxx
                if (cleanPaste.startsWith('8')) {
                    e.target.value = '628' + cleanPaste.substring(1);
                } else {
                    e.target.value = '628' + cleanPaste;
                }
                e.target.dispatchEvent(new Event('input')); // Trigger input event
            });

            // Tab switching
            emailTab.addEventListener('click', function() {
                // Switch tabs
                emailTab.classList.add('text-gray-700', 'border-indigo-500');
                emailTab.classList.remove('text-gray-500', 'border-transparent');
                phoneTab.classList.add('text-gray-500', 'border-transparent');
                phoneTab.classList.remove('text-gray-700', 'border-indigo-500');

                // Switch forms
                emailForm.style.display = 'block';
                phoneForm.style.display = 'none';
                otpForm.style.display = 'none';
            });

            phoneTab.addEventListener('click', function() {
                // Switch tabs
                phoneTab.classList.add('text-gray-700', 'border-indigo-500');
                phoneTab.classList.remove('text-gray-500', 'border-transparent');
                emailTab.classList.add('text-gray-500', 'border-transparent');
                emailTab.classList.remove('text-gray-700', 'border-indigo-500');

                // Switch forms
                emailForm.style.display = 'none';
                phoneForm.style.display = 'block';
                otpForm.style.display = 'none';
            });

            // Back to phone form
            document.getElementById('back-to-phone').addEventListener('click', function() {
                phoneForm.style.display = 'block';
                otpForm.style.display = 'none';
            });

            // Handle phone form submission
            phoneForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(phoneForm);
                const submitButton = phoneForm.querySelector('button[type="submit"]');
                
                // Disable button saat loading
                submitButton.disabled = true;
                submitButton.textContent = 'Mengirim...';
                
                fetch('{{ route("login.phone") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Switch to OTP form
                        document.getElementById('otp_nomor_hp').value = formData.get('nomor_hp');
                        document.getElementById('phone-display').textContent = formData.get('nomor_hp');
                        phoneForm.style.display = 'none';
                        otpForm.style.display = 'block';
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                })
                .finally(() => {
                    // Re-enable button
                    submitButton.disabled = false;
                    submitButton.textContent = '{{ __("Kirim Kode OTP") }}';
                });
            });

            // Resend OTP
            document.getElementById('resend-otp').addEventListener('click', function() {
                const nomor_hp = document.getElementById('otp_nomor_hp').value;
                const resendButton = this;
                
                resendButton.disabled = true;
                resendButton.textContent = 'Mengirim...';
                
                fetch('{{ route("login.phone.resend") }}', {
                    method: 'POST',
                    body: JSON.stringify({ nomor_hp: nomor_hp }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message || 'Kode OTP telah dikirim ulang');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                })
                .finally(() => {
                    resendButton.disabled = false;
                    resendButton.textContent = '{{ __("Kirim Ulang") }}';
                });
            });

            // Set default tab: phone
            emailTab.classList.add('text-gray-500', 'border-transparent');
            emailTab.classList.remove('text-gray-700', 'border-indigo-500');
            phoneTab.classList.add('text-gray-700', 'border-indigo-500');
            phoneTab.classList.remove('text-gray-500', 'border-transparent');
            emailForm.style.display = 'none';
            phoneForm.style.display = 'block';
            otpForm.style.display = 'none';
        });
    </script>
</x-guest-layout>