<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Aduan Jaringan IPAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

<div class="w-full max-w-2xl">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 px-8 py-6">
            <h1 class="text-2xl font-bold text-white">Form Aduan Jaringan IPAL</h1>
            <p class="text-blue-100 mt-1 text-sm">Laporkan kondisi pipa atau manhole yang bermasalah</p>
        </div>

        <!-- Success State -->
        <div id="successState" class="hidden px-8 py-10 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Aduan Berhasil Dikirim!</h2>
            <p class="text-gray-500 mb-4">Nomor tiket Anda:</p>
            <div class="inline-block bg-blue-50 border border-blue-200 rounded-lg px-6 py-3">
                <span id="nomorTiket" class="text-2xl font-bold text-blue-600 font-mono tracking-widest"></span>
            </div>
            <p class="text-gray-400 text-sm mt-4">Simpan nomor tiket ini untuk memantau perkembangan aduan Anda.</p>
            <button onclick="resetForm()"
                class="mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Kirim Aduan Lain
            </button>
        </div>

        <!-- Form -->
        <form id="aduanForm" class="px-8 py-6 space-y-5">
            <!-- Pilih Jenis Aset -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Aset <span class="text-red-500">*</span></label>
                <div class="flex gap-3">
                    <label class="flex-1">
                        <input type="radio" name="jenis_aset" value="pipa" id="radioPipa" class="peer hidden" checked>
                        <div class="peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 border-2 border-gray-200 rounded-lg p-3 text-center cursor-pointer transition text-sm font-medium text-gray-600 hover:border-blue-400">
                            Pipa
                        </div>
                    </label>
                    <label class="flex-1">
                        <input type="radio" name="jenis_aset" value="manhole" id="radioManhole" class="peer hidden">
                        <div class="peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 border-2 border-gray-200 rounded-lg p-3 text-center cursor-pointer transition text-sm font-medium text-gray-600 hover:border-blue-400">
                            Manhole
                        </div>
                    </label>
                </div>
            </div>

            <!-- Pilih Pipa -->
            <div id="sectionPipa">
                <label for="pipaDrop" class="block text-sm font-semibold text-gray-700 mb-1">
                    Pilih Pipa <span class="text-red-500">*</span>
                </label>
                <select id="pipaDrop" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    <option value="">-- Memuat data pipa... --</option>
                </select>
                <p class="text-xs text-gray-400 mt-1" id="pipaInfo"></p>
            </div>

            <!-- Pilih Manhole -->
            <div id="sectionManhole" class="hidden">
                <label for="manholeDrop" class="block text-sm font-semibold text-gray-700 mb-1">
                    Pilih Manhole <span class="text-red-500">*</span>
                </label>
                <select id="manholeDrop" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    <option value="">-- Memuat data manhole... --</option>
                </select>
                <p class="text-xs text-gray-400 mt-1" id="manholeInfo"></p>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-1">
                    Deskripsi Masalah <span class="text-red-500">*</span>
                </label>
                <textarea id="deskripsi" rows="4" maxlength="5000"
                    placeholder="Jelaskan kondisi yang bermasalah secara detail..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                <p class="text-xs text-gray-400 mt-1"><span id="charCount">0</span>/5000 karakter</p>
            </div>

            <!-- Upload Foto -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Foto Dokumentasi <span class="text-gray-400 font-normal">(opsional, maks. 5 foto)</span>
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition cursor-pointer"
                     onclick="document.getElementById('fotoInput').click()">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Klik untuk memilih foto</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, JPEG, PNG, WEBP • Setiap foto akan dikompresi maks. {{ config('ipal.aduan_foto_max_kb_user') >= 1024 ? (config('ipal.aduan_foto_max_kb_user') / 1024) . ' MB' : config('ipal.aduan_foto_max_kb_user') . ' KB' }}</p>
                </div>
                <input type="file" id="fotoInput" accept="image/jpeg,image/jpg,image/png,image/webp"
                    multiple class="hidden" onchange="previewFoto(this)">
                <div id="fotoPreview" class="flex flex-wrap gap-2 mt-3"></div>
            </div>

            @if(config('ipal.aduan_captcha_enabled'))
            <!-- Captcha -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Verifikasi <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 mb-1">Berapa hasil dari:</p>
                        <p id="captchaQuestion" class="text-lg font-bold text-gray-800 font-mono">Memuat...</p>
                    </div>
                    <input type="number" id="captchaAnswer" min="0" max="99"
                        placeholder="Jawaban"
                        class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="button" onclick="loadCaptcha()" title="Muat ulang captcha"
                        class="p-2 text-gray-400 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            <!-- Error Message -->
            <div id="errorMsg" class="hidden bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-600"></div>

            <!-- Submit -->
            <button type="submit" id="submitBtn"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <span id="submitText">Kirim Aduan</span>
                <svg id="submitSpinner" class="hidden animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </button>
        </form>
    </div>

    <p class="text-center text-gray-400 text-xs mt-4">
        Sistem Informasi Manajemen Laboratorium (SIMLAB) — BPJK
    </p>
</div>

<script>
const BASE_URL = '{{ url('/') }}';
const MAX_FOTO = {{ config('ipal.aduan_max_foto') }};
const MAX_KB_USER = {{ config('ipal.aduan_foto_max_kb_user') }};
const CAPTCHA_ENABLED = {{ config('ipal.aduan_captcha_enabled') ? 'true' : 'false' }};
let pipas = [];
let manholes = [];
let selectedFiles = [];
let captchaToken = null;

@if(config('ipal.aduan_captcha_enabled'))
async function loadCaptcha() {
    document.getElementById('captchaQuestion').textContent = 'Memuat...';
    document.getElementById('captchaAnswer').value = '';
    captchaToken = null;
    try {
        const res  = await fetch(`${BASE_URL}/api/ipal/aduan/captcha`);
        const json = await res.json();
        if (json.success) {
            captchaToken = json.data.token;
            document.getElementById('captchaQuestion').textContent = json.data.question + ' = ?';
        } else {
            document.getElementById('captchaQuestion').textContent = 'Gagal memuat. Klik ↻';
        }
    } catch (e) {
        document.getElementById('captchaQuestion').textContent = 'Gagal memuat. Klik ↻';
    }
}
@endif

async function loadDropdowns() {
    try {
        const [pipRes, mhRes] = await Promise.all([
            fetch(`${BASE_URL}/api/ipal/pipes?per_page=200`),
            fetch(`${BASE_URL}/api/ipal/manholes?per_page=200`),
        ]);
        const pipData = await pipRes.json();
        const mhData  = await mhRes.json();

        pipas     = pipData.data?.data ?? [];
        manholes  = mhData.data?.data ?? [];

        populateSelect('pipaDrop', pipas, 'id', p => `${p.kode_pipa} — ${p.wilayah ?? '-'} (Status: ${p.status})`);
        populateSelect('manholeDrop', manholes, 'id', m => `${m.kode_manhole} — ${m.wilayah ?? '-'} (Status: ${m.status})`);
    } catch (e) {
        document.getElementById('pipaDrop').innerHTML = '<option value="">Gagal memuat data pipa</option>';
        document.getElementById('manholeDrop').innerHTML = '<option value="">Gagal memuat data manhole</option>';
    }
}

function populateSelect(id, items, valKey, labelFn) {
    const sel = document.getElementById(id);
    sel.innerHTML = '<option value="">-- Pilih --</option>';
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value       = item[valKey];
        opt.textContent = labelFn(item);
        sel.appendChild(opt);
    });
}

document.querySelectorAll('input[name="jenis_aset"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const isPipa = radio.value === 'pipa';
        document.getElementById('sectionPipa').classList.toggle('hidden', !isPipa);
        document.getElementById('sectionManhole').classList.toggle('hidden', isPipa);
    });
});

document.getElementById('deskripsi').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

document.getElementById('pipaDrop').addEventListener('change', function() {
    const p = pipas.find(x => x.id == this.value);
    document.getElementById('pipaInfo').textContent = p
        ? `Material: ${p.material ?? '-'} | Panjang: ${p.length_km ?? '-'} km | Fungsi: ${p.fungsi ?? '-'}`
        : '';
});

document.getElementById('manholeDrop').addEventListener('change', function() {
    const m = manholes.find(x => x.id == this.value);
    document.getElementById('manholeInfo').textContent = m
        ? `Kondisi: ${m.kondisi_mh ?? '-'} | Kecamatan: ${m.kecamatan ?? '-'} | Desa: ${m.desa ?? '-'}`
        : '';
});

function previewFoto(input) {
    if (input.files.length > MAX_FOTO) {
        showError(`Maksimal ${MAX_FOTO} foto yang dapat diunggah.`);
        input.value = '';
        return;
    }
    selectedFiles = Array.from(input.files);
    const preview = document.getElementById('fotoPreview');
    preview.innerHTML = '';
    selectedFiles.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                <span class="absolute -top-1 -right-1 bg-gray-800 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center cursor-pointer"
                    onclick="removeFile(${i})">✕</span>
                <p class="text-xs text-gray-400 mt-1 text-center truncate w-20">${(file.size/1024).toFixed(0)} KB</p>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    document.getElementById('fotoInput').files = dt.files;
    previewFoto(document.getElementById('fotoInput'));
}

function showError(msg) {
    const el = document.getElementById('errorMsg');
    el.textContent = msg;
    el.classList.remove('hidden');
}

function hideError() {
    document.getElementById('errorMsg').classList.add('hidden');
}

function setLoading(loading) {
    document.getElementById('submitBtn').disabled = loading;
    document.getElementById('submitText').textContent = loading ? 'Mengirim...' : 'Kirim Aduan';
    document.getElementById('submitSpinner').classList.toggle('hidden', !loading);
}

function resetForm() {
    document.getElementById('aduanForm').classList.remove('hidden');
    document.getElementById('successState').classList.add('hidden');
    document.getElementById('aduanForm').reset();
    document.getElementById('fotoPreview').innerHTML = '';
    document.getElementById('charCount').textContent = '0';
    document.getElementById('pipaInfo').textContent = '';
    document.getElementById('manholeInfo').textContent = '';
    selectedFiles = [];
    hideError();
    if (CAPTCHA_ENABLED) loadCaptcha();
}

document.getElementById('aduanForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    hideError();

    const jenis    = document.querySelector('input[name="jenis_aset"]:checked').value;
    const pipaId   = document.getElementById('pipaDrop').value;
    const manhId   = document.getElementById('manholeDrop').value;
    const deskripsi = document.getElementById('deskripsi').value.trim();

    if (jenis === 'pipa' && !pipaId) {
        return showError('Silakan pilih pipa terlebih dahulu.');
    }
    if (jenis === 'manhole' && !manhId) {
        return showError('Silakan pilih manhole terlebih dahulu.');
    }
    if (!deskripsi) {
        return showError('Deskripsi masalah wajib diisi.');
    }

    const formData = new FormData();
    if (jenis === 'pipa')    formData.append('pipa_id', pipaId);
    if (jenis === 'manhole') formData.append('manhole_id', manhId);
    formData.append('deskripsi', deskripsi);

    if (CAPTCHA_ENABLED) {
        if (!captchaToken) {
            return showError('Captcha belum dimuat. Klik tombol refresh (↻) untuk memuat ulang.');
        }
        const captchaAnswer = document.getElementById('captchaAnswer').value.trim();
        if (!captchaAnswer) {
            return showError('Jawaban captcha wajib diisi.');
        }
        formData.append('captcha_token', captchaToken);
        formData.append('captcha_answer', captchaAnswer);
    }

    const fotoInput = document.getElementById('fotoInput');
    Array.from(fotoInput.files).forEach((file, i) => {
        formData.append(`foto[${i}]`, file);
    });

    setLoading(true);
    try {
        const res  = await fetch(`${BASE_URL}/api/ipal/aduan`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData,
        });
        const json = await res.json();

        if (res.ok && json.success) {
            document.getElementById('nomorTiket').textContent = json.data.nomor_tiket;
            this.classList.add('hidden');
            document.getElementById('successState').classList.remove('hidden');
        } else {
            const errors = json.data;
            if (errors && typeof errors === 'object') {
                const msgs = Object.values(errors).flat().join(' ');
                showError(msgs);
                if (CAPTCHA_ENABLED && errors.captcha_answer) loadCaptcha();
            } else {
                showError(json.message ?? 'Terjadi kesalahan. Silakan coba lagi.');
            }
        }
    } catch (err) {
        showError('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
    } finally {
        setLoading(false);
    }
});

loadDropdowns();
if (CAPTCHA_ENABLED) loadCaptcha();
</script>
</body>
</html>
