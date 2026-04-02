@extends('ipal::layouts.main')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!--begin::Card-->
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold mb-0">Daftar Aduan</h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Filter Form-->
                    <form method="GET" action="{{ route('ipal.aduan.index') }}" class="d-flex gap-2 align-items-center">
                        <select name="status_aduan" class="form-select form-select-sm w-150px" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach(['masuk','verifikasi','proses','ditolak','selesai'] as $st)
                                @php
                                    $statusLabel = $st === 'proses' ? 'Diproses' : ucfirst($st);
                                @endphp
                                <option value="{{ $st }}" @selected(request('status_aduan') === $st)>{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                        <div class="position-relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nomor tiket / deskripsi..."
                                class="form-control form-control-sm w-250px ps-9">
                            <span class="position-absolute translate-middle-y top-50 ms-3">
                                <i class="ki-outline ki-magnifier fs-5 text-gray-500"></i>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                        @if(request()->hasAny(['status_aduan','search']))
                            <a href="{{ route('ipal.aduan.index') }}" class="btn btn-sm btn-light">Reset</a>
                        @endif
                    </form>
                    <!--end::Filter Form-->
                </div>
            </div>

            <div class="card-body pt-0">
                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center mb-4">
                        <i class="ki-outline ki-check-circle fs-2hx text-success me-3"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <!--begin::Table-->
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-90px">Nomor Tiket</th>
                                <th class="min-w-70px">Jenis</th>
                                <th class="min-w-100px">Kode Aset</th>
                                <th class="min-w-80px">Wilayah</th>
                                <th class="min-w-80px">Status</th>
                                <th class="min-w-60px text-center">Foto</th>
                                <th class="min-w-100px">Tanggal</th>
                                <th class="min-w-70px text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($aduan as $item)
                            <tr>
                                <td>
                                    <span class="text-gray-800 fw-bold font-monospace">{{ $item->nomor_tiket }}</span>
                                </td>
                                <td>
                                    @if($item->pipa_id)
                                        <span class="badge badge-light-info">Pipa</span>
                                    @elseif($item->manhole_id)
                                        <span class="badge badge-light-warning">Manhole</span>
                                    @else
                                        <span class="badge badge-light">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-gray-700">
                                        {{ $item->pipa?->kode_pipa ?? $item->manhole?->kode_manhole ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $item->pipa?->wilayah ?? $item->manhole?->wilayah ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeMap = [
                                            'masuk'      => 'badge-light-primary',
                                            'verifikasi' => 'badge-light-warning',
                                            'proses'     => 'badge-light-info',
                                            'ditolak'    => 'badge-light-danger',
                                            'selesai'    => 'badge-light-success',
                                        ];
                                        $badge = $badgeMap[$item->status_aduan] ?? 'badge-light';
                                        $statusLabel = $item->status_aduan === 'proses' ? 'Diproses' : ucfirst($item->status_aduan);
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ $item->dokumentasi_count }}</span>
                                </td>
                                <td class="text-gray-500 fs-7">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('ipal.aduan.show', $item->id) }}"
                                        class="btn btn-sm btn-light btn-active-light-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-10">
                                    <i class="ki-outline ki-search-list fs-3x text-gray-300 d-block mb-3"></i>
                                    Belum ada aduan yang masuk.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!--end::Table-->

                <!--begin::Pagination-->
                <div class="d-flex justify-content-end mt-4">
                    {{ $aduan->links() }}
                </div>
                <!--end::Pagination-->
            </div>
        </div>
        <!--end::Card-->

    </div>
</div>
@endsection
