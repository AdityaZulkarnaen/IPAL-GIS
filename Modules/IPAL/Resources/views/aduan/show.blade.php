@extends('ipal::layouts.main')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-5">
            <a href="{{ route('ipal.aduan.index') }}" class="btn btn-sm btn-light">
                <i class="ki-outline ki-arrow-left fs-5 me-1"></i>Kembali ke Daftar
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center mb-5">
                <i class="ki-outline ki-check-circle fs-2hx text-success me-3"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center mb-5">
                <i class="ki-outline ki-cross-circle fs-2hx text-danger me-3"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="row g-5">
            <!--begin::Left Column - Info + Foto-->
            <div class="col-xl-7">

                <!--begin::Info Card-->
                <div class="card mb-5">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold">Informasi Aduan</h3>
                        <div class="card-toolbar">
                            @php
                                $badgeMap = [
                                    'masuk'      => 'badge-light-primary',
                                    'verifikasi' => 'badge-light-warning',
                                    'proses'     => 'badge-light-info',
                                    'selesai'    => 'badge-light-success',
                                ];
                            @endphp
                            <span class="badge fs-6 {{ $badgeMap[$aduan->status_aduan] ?? 'badge-light' }}">
                                {{ ucfirst($aduan->status_aduan) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table table-borderless fs-6">
                            <tr>
                                <td class="text-muted fw-semibold w-150px py-2">Nomor Tiket</td>
                                <td class="fw-bold font-monospace py-2">{{ $aduan->nomor_tiket }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2">Jenis Aset</td>
                                <td class="py-2">
                                    @if($aduan->pipa_id)
                                        <span class="badge badge-light-info">Pipa</span>
                                    @elseif($aduan->manhole_id)
                                        <span class="badge badge-light-warning">Manhole</span>
                                    @endif
                                </td>
                            </tr>
                            @if($aduan->pipa)
                            <tr>
                                <td class="text-muted fw-semibold py-2">Kode Pipa</td>
                                <td class="py-2">{{ $aduan->pipa->kode_pipa }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2">Status Pipa Saat Ini</td>
                                <td class="py-2">
                                    <span class="badge badge-light-{{ $aduan->pipa->status === 'aman' ? 'success' : ($aduan->pipa->status === 'rusak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($aduan->pipa->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                            @if($aduan->manhole)
                            <tr>
                                <td class="text-muted fw-semibold py-2">Kode Manhole</td>
                                <td class="py-2">{{ $aduan->manhole->kode_manhole }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2">Status Manhole Saat Ini</td>
                                <td class="py-2">
                                    <span class="badge badge-light-{{ $aduan->manhole->status === 'aman' ? 'success' : ($aduan->manhole->status === 'rusak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($aduan->manhole->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted fw-semibold py-2">Tanggal Masuk</td>
                                <td class="py-2">{{ $aduan->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2 align-top">Deskripsi</td>
                                <td class="py-2">{{ $aduan->deskripsi }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <!--end::Info Card-->

                <!--begin::Foto Dokumentasi-->
                @php
                    $fotoPelapor = $aduan->dokumentasi->where('tipe_pengunggah', 'pelapor');
                    $fotoAdmin   = $aduan->dokumentasi->where('tipe_pengunggah', 'admin');
                @endphp

                @if($fotoPelapor->isNotEmpty())
                <div class="card mb-5">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold">Foto dari Pelapor</h3>
                        <span class="card-toolbar text-muted fs-7">{{ $fotoPelapor->count() }} foto</span>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($fotoPelapor as $foto)
                            <a href="{{ Storage::url($foto->file_path) }}" target="_blank">
                                <img src="{{ Storage::url($foto->file_path) }}"
                                    alt="{{ $foto->file_name }}"
                                    class="rounded w-150px h-150px object-fit-cover border border-gray-200">
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if($fotoAdmin->isNotEmpty())
                <div class="card mb-5">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold">Foto Tindak Lanjut Admin</h3>
                        <span class="card-toolbar text-muted fs-7">{{ $fotoAdmin->count() }} foto</span>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex flex-wrap gap-3">
                            @foreach($fotoAdmin as $foto)
                            <a href="{{ Storage::url($foto->file_path) }}" target="_blank">
                                <img src="{{ Storage::url($foto->file_path) }}"
                                    alt="{{ $foto->file_name }}"
                                    class="rounded w-150px h-150px object-fit-cover border border-gray-200">
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if($aduan->dokumentasi->isEmpty())
                <div class="card mb-5">
                    <div class="card-body text-center text-muted py-8">
                        <i class="ki-outline ki-picture fs-3x text-gray-300 d-block mb-2"></i>
                        Belum ada foto dokumentasi.
                    </div>
                </div>
                @endif
                <!--end::Foto Dokumentasi-->

            </div>
            <!--end::Left Column-->

            <!--begin::Right Column - Update Form + History-->
            <div class="col-xl-5">

                <!--begin::Update Status Form-->
                <div class="card mb-5">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold">Update Status Aduan</h3>
                    </div>
                    <div class="card-body pt-0">
                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0 ps-4">
                                    @foreach($errors->all() as $err)
                                        <li class="fs-7">{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('ipal.aduan.updateStatus', $aduan->id) }}"
                              method="POST" enctype="multipart/form-data">
                            @csrf

                            <!--begin::Status Aduan-->
                            <div class="mb-4">
                                <label class="form-label fw-semibold required">Status Aduan</label>
                                <select name="status_aduan" class="form-select form-select-sm" required>
                                    @foreach(['masuk','verifikasi','proses','selesai'] as $st)
                                        <option value="{{ $st }}" @selected(old('status_aduan', $aduan->status_aduan) === $st)>
                                            {{ ucfirst($st) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!--end::Status Aduan-->

                            <!--begin::Status Aset-->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Update Status Aset
                                    <span class="text-muted fs-8 fw-normal ms-1">(opsional)</span>
                                </label>
                                <select name="status_aset" class="form-select form-select-sm">
                                    <option value="">-- Tidak diubah --</option>
                                    <option value="aman" @selected(old('status_aset') === 'aman')>Aman</option>
                                    <option value="rusak" @selected(old('status_aset') === 'rusak')>Rusak</option>
                                    <option value="dalam perbaikan" @selected(old('status_aset') === 'dalam perbaikan')>Dalam Perbaikan</option>
                                </select>
                                <div class="text-muted fs-8 mt-1">
                                    Jika dipilih, status {{ $aduan->pipa_id ? 'pipa' : 'manhole' }} akan diperbarui.
                                </div>
                            </div>
                            <!--end::Status Aset-->

                            <!--begin::Catatan-->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Catatan Tindak Lanjut
                                    <span class="text-muted fs-8 fw-normal ms-1">(opsional)</span>
                                </label>
                                <textarea name="catatan_tindak_lanjut" rows="3" maxlength="5000"
                                    class="form-control form-control-sm"
                                    placeholder="Tuliskan catatan atau tindakan yang telah dilakukan...">{{ old('catatan_tindak_lanjut') }}</textarea>
                            </div>
                            <!--end::Catatan-->

                            <!--begin::Foto Admin-->
                            <div class="mb-5">
                                <label class="form-label fw-semibold">
                                    Foto Tindak Lanjut
                                    <span class="text-muted fs-8 fw-normal ms-1">(opsional, maks. 1 foto, dikompres ke maks. 1 MB)</span>
                                </label>
                                <input type="file" name="foto" accept="image/jpeg,image/jpg,image/png,image/webp"
                                    class="form-control form-control-sm" id="adminFotoInput"
                                    onchange="previewAdminFoto(this)">
                                <div id="adminFotoPreview" class="mt-2"></div>
                            </div>
                            <!--end::Foto Admin-->

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ki-outline ki-check fs-5 me-1"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
                <!--end::Update Status Form-->

                <!--begin::History Timeline-->
                @if($aduan->history->isNotEmpty())
                <div class="card">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title fw-bold">Riwayat Perubahan Status</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="timeline-label">
                            @foreach($aduan->history as $h)
                            <div class="timeline-item mb-5">
                                <div class="timeline-label fw-bold text-gray-800 fs-7">
                                    {{ \Carbon\Carbon::parse($h->created_at)->format('d M Y') }}<br>
                                    <span class="text-muted fw-normal">{{ \Carbon\Carbon::parse($h->created_at)->format('H:i') }}</span>
                                </div>
                                <div class="timeline-badge">
                                    <i class="fa fa-circle text-{{ $h->status_sesudah === 'selesai' ? 'success' : ($h->status_sesudah === 'proses' ? 'info' : 'warning') }} fs-7"></i>
                                </div>
                                <div class="timeline-content ps-3">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge badge-light-secondary">{{ ucfirst($h->status_sebelumnya) }}</span>
                                        <i class="ki-outline ki-arrow-right fs-7 text-muted"></i>
                                        <span class="badge badge-light-primary">{{ ucfirst($h->status_sesudah) }}</span>
                                    </div>
                                    <div class="text-muted fs-8">oleh: {{ $h->admin?->name ?? 'Admin' }}</div>
                                    @if($h->catatan_tindak_lanjut)
                                        <div class="text-gray-700 fs-7 mt-1 fst-italic">"{{ $h->catatan_tindak_lanjut }}"</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-body text-center text-muted py-8">
                        <i class="ki-outline ki-time fs-3x text-gray-300 d-block mb-2"></i>
                        Belum ada riwayat perubahan status.
                    </div>
                </div>
                @endif
                <!--end::History Timeline-->

            </div>
            <!--end::Right Column-->
        </div>

    </div>
</div>
@endsection

@push('ipal-scripts')
<script>
function previewAdminFoto(input) {
    const preview = document.getElementById('adminFotoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `
                <img src="${e.target.result}" class="rounded w-100px h-100px object-fit-cover border border-gray-200 mt-1">
                <p class="text-muted fs-8 mt-1">${(input.files[0].size/1024).toFixed(0)} KB (akan dikompres maks. 1 MB)</p>
            `;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
    }
}
</script>
@endpush
