@extends('ipal::layouts.main')

@section('content')
<div id="kt_app_content" class="app-content bg-white pt-2 flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        @php
            $statusBadgeMap = [
                'masuk' => 'aduan-status aduan-status-menunggu',
                'verifikasi' => 'aduan-status aduan-status-menunggu',
                'proses' => 'aduan-status aduan-status-diproses',
                'ditolak' => 'aduan-status aduan-status-ditolak',
                'selesai' => 'aduan-status aduan-status-selesai',
            ];
        @endphp

        <div class="aduan-breadcrumb-wrap mb-5">
            <a href="{{ route('ipal.dashboard') }}" class="aduan-breadcrumb-link">IPAL</a>
            <span class="aduan-breadcrumb-sep">
                <svg width="5" height="8" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.175736 0.180086C0.410051 -0.0600286 0.78995 -0.0600286 1.02426 0.180086L5.02426 4.2791C5.25858 4.51921 5.25858 4.90851 5.02426 5.14863L1.24649 9.01991C1.01217 9.26003 0.632273 9.26003 0.397958 9.01991C0.163644 8.7798 0.163644 8.3905 0.397958 8.15038L3.75147 4.71386L0.175736 1.04962C-0.0585787 0.809503 -0.0585787 0.4202 0.175736 0.180086Z" fill="#99A1B7"/>
                </svg>
            </span>
            <span class="aduan-breadcrumb-current">Aduan</span>
        </div>

        <div class="mb-5">
            <h2 class="aduan-page-title mb-1">Manajemen Aduan</h2>
            <p class="aduan-page-subtitle mb-0">Pantau status, verifikasi, dan tindak lanjut setiap aduan jaringan IPAL.</p>
        </div>

        <div class="card aduan-list-card border border-slate-200 rounded-xl shadow-[0_10px_28px_rgba(15,23,42,0.04)]">
            <div class="card-header aduan-list-header border-0 min-h-0">
                <div class="card-title mb-0">
                    <h3 class="aduan-list-title mb-0">Daftar Aduan Masuk</h3>
                </div>
                <div class="card-toolbar">
                    <form method="GET" action="{{ route('ipal.aduan.index') }}" class="d-flex gap-4 align-items-center flex-wrap justify-content-end">
                        <select name="status_aduan" class="form-select form-select-sm aduan-filter-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach(['masuk','verifikasi','proses','ditolak','selesai'] as $st)
                                @php
                                    $statusLabel = match ($st) {
                                        'masuk', 'verifikasi' => 'Menunggu',
                                        'proses' => 'Diproses',
                                        'ditolak' => 'Ditolak',
                                        'selesai' => 'Selesai',
                                        default => ucfirst($st),
                                    };
                                @endphp
                                <option value="{{ $st }}" @selected(request('status_aduan') === $st)>{{ $statusLabel }}</option>
                            @endforeach
                        </select>

                        <div class="position-relative aduan-filter-search-wrap">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari ID Aset, kecamatan"
                                class="form-control form-control-sm aduan-filter-search ps-9">
                            <span class="position-absolute translate-middle-y top-50 ms-3 text-gray-400">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.1" d="M13.1999 7.43319C13.1999 10.6548 10.5882 13.2665 7.36654 13.2665C4.14488 13.2665 1.5332 10.6548 1.5332 7.43319C1.5332 4.21153 4.14488 1.59985 7.36654 1.59985C10.5882 1.59985 13.1999 4.21153 13.1999 7.43319Z" fill="#99A1B7"/>
                                    <path d="M7.37891 1.37964C10.6925 1.37964 13.3787 4.06611 13.3789 7.37964C13.3789 8.8323 12.862 10.1638 12.0029 11.2019L14.4463 13.6462C14.6676 13.8675 14.6676 14.2257 14.4463 14.447C14.225 14.6683 13.8668 14.6683 13.6455 14.447L11.2012 12.0037C10.1631 12.8627 8.83155 13.3796 7.37891 13.3796C4.06539 13.3794 1.37891 10.6932 1.37891 7.37964C1.37911 4.06625 4.06552 1.37987 7.37891 1.37964ZM7.37891 2.49194C4.67968 2.49217 2.49142 4.6804 2.49121 7.37964C2.49121 10.0791 4.67955 12.2681 7.37891 12.2683C10.0785 12.2683 12.2676 10.0792 12.2676 7.37964C12.2674 4.68026 10.0783 2.49194 7.37891 2.49194Z" fill="#99A1B7"/>
                                </svg>
                            </span>
                        </div>

                        {{-- @if(request()->hasAny(['status_aduan','search']))
                            <a href="{{ route('ipal.aduan.index') }}" class="btn btn-sm btn-light aduan-btn-action">Reset</a>
                        @endif --}}
                    </form>
                </div>
            </div>

            <div class="card-body pt-0 pb-4">
                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center mb-4">
                        <i class="ki-outline ki-check-circle fs-2hx text-success me-3"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed mb-0 aduan-table">
                        <colgroup>
                            <col style="width: 24%;">
                            <col style="width: 10%;">
                            <col style="width: 12%;">
                            <col style="width: 18%;">
                            <col style="width: 18%;">
                            <col style="width: 10%;">
                            <col style="width: 8%;">
                        </colgroup>
                        <thead class="aduan-table-head">
                            <tr class="text-start fw-semibold text-uppercase gs-0">
                                <th>Nomor Tiket</th>
                                <th>Tipe</th>
                                <th>ID Aset</th>
                                <th>Wilayah</th>
                                <th>Tanggal Masuk</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="aduan-table-body">
                            @forelse($aduan as $item)
                            <tr>
                                <td>
                                    <span class="aduan-ticket">{{ $item->nomor_tiket }}</span>
                                </td>
                                <td>
                                    @if($item->pipa_id)
                                        <span class="badge badge-light-info">PIPA</span>
                                    @elseif($item->manhole_id)
                                        <span class="badge badge-light-info">MANHOLE</span>
                                    @else
                                        <span class="badge badge-light">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="aduan-asset-id">
                                        {{ $item->pipa?->kode_pipa ?? $item->manhole?->kode_manhole ?? '-' }}
                                    </span>
                                </td>
                                <td class="aduan-region">{{ $item->pipa?->wilayah ?? $item->manhole?->wilayah ?? '-' }}</td>
                                <td class="aduan-date">{{ $item->created_at->format('d F Y, H.i') }}</td>
                                <td>
                                    @php
                                        $statusLabel = match ($item->status_aduan) {
                                            'masuk', 'verifikasi' => 'MENUNGGU',
                                            'proses' => 'DIPROSES',
                                            'ditolak' => 'DITOLAK',
                                            'selesai' => 'SELESAI',
                                            default => strtoupper($item->status_aduan),
                                        };
                                        $statusClass = $statusBadgeMap[$item->status_aduan] ?? 'aduan-status';
                                    @endphp
                                    <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('ipal.aduan.show', $item->id) }}" class="btn btn-sm btn-light aduan-detail-btn">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-10">
                                    <i class="ki-outline ki-search-list fs-3x text-gray-300 d-block mb-3"></i>
                                    Aduan tidak ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="aduan-mobile-list">
                    @forelse($aduan as $item)
                        @php
                            $statusLabel = match ($item->status_aduan) {
                                'masuk', 'verifikasi' => 'MENUNGGU',
                                'proses' => 'DIPROSES',
                                'ditolak' => 'DITOLAK',
                                'selesai' => 'SELESAI',
                                default => strtoupper($item->status_aduan),
                            };
                            $statusClass = $statusBadgeMap[$item->status_aduan] ?? 'aduan-status';
                            $assetCode = $item->pipa?->kode_pipa ?? $item->manhole?->kode_manhole ?? '-';
                            $typeLabel = $item->pipa_id ? 'PIPA' : ($item->manhole_id ? 'MH' : '-');
                        @endphp

                        <div class="aduan-mobile-item">
                            <div class="aduan-mobile-top">
                                <div class="aduan-mobile-date">{{ $item->created_at->format('d F Y, H.i') }}</div>
                                <span class="badge badge-light-info aduan-mobile-type">{{ $typeLabel }}</span>
                                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                                <a href="{{ route('ipal.aduan.show', $item->id) }}" class="btn btn-sm btn-light aduan-detail-btn">Detail</a>
                            </div>
                            <div class="aduan-mobile-ticket">{{ $item->nomor_tiket }}</div>
                            <div class="aduan-mobile-asset">{{ $assetCode }}</div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-10">
                            <i class="ki-outline ki-search-list fs-3x text-gray-300 d-block mb-3"></i>
                            Aduan tidak ditemukan.
                        </div>
                    @endforelse
                </div>

                <div class="aduan-pagination-wrap mt-3">
                    <div class="aduan-pagination-left">
                        <span>Show</span>
                        <form method="GET" action="{{ route('ipal.aduan.index') }}" class="d-inline-flex align-items-center">
                            <input type="hidden" name="status_aduan" value="{{ request('status_aduan') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <select name="per_page" class="form-select form-select-sm aduan-per-page" onchange="this.form.submit()">
                                @foreach([5, 10, 15, 25, 50] as $size)
                                    <option value="{{ $size }}" @selected($aduan->perPage() === $size)>{{ $size }}</option>
                                @endforeach
                            </select>
                        </form>
                        <span>per page</span>
                    </div>

                    <div class="aduan-pagination-right">
                        <span class="aduan-pagination-summary">
                            {{ $aduan->firstItem() ?? 0 }}-{{ $aduan->lastItem() ?? 0 }} of {{ $aduan->total() }}
                        </span>

                        @php
                            $currentPage = $aduan->currentPage();
                            $lastPage = $aduan->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $startPage + 4);
                            $startPage = max(1, $endPage - 4);
                        @endphp

                        <div class="aduan-page-links">
                            <a href="{{ $aduan->onFirstPage() ? '#' : $aduan->previousPageUrl() }}" class="aduan-page-nav {{ $aduan->onFirstPage() ? 'is-disabled' : '' }}" @if($aduan->onFirstPage()) aria-disabled="true" @endif>
                                <i class="ki-outline ki-left fs-7"></i>
                            </a>

                            @for($page = $startPage; $page <= $endPage; $page++)
                                <a href="{{ $aduan->url($page) }}" class="aduan-page-link {{ $page === $currentPage ? 'is-active' : '' }}">{{ $page }}</a>
                            @endfor

                            <a href="{{ $aduan->hasMorePages() ? $aduan->nextPageUrl() : '#' }}" class="aduan-page-nav {{ !$aduan->hasMorePages() ? 'is-disabled' : '' }}" @if(!$aduan->hasMorePages()) aria-disabled="true" @endif>
                                <i class="ki-outline ki-right fs-7"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('ipal-styles')
<style>
    .aduan-breadcrumb-wrap {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .aduan-breadcrumb-link,
    .aduan-breadcrumb-current {
        font-size: 12px;
        line-height: 1.2;
        color: #64748b;
        font-weight: 500;
        text-decoration: none;
    }

    .aduan-breadcrumb-link:hover {
        color: #334155;
    }

    .aduan-breadcrumb-sep {
        display: inline-flex;
        align-items: center;
        color: #94a3b8;
    }

    .aduan-page-title {
        font-size: 24px;
        line-height: 1.25;
        color: #0f172a;
        font-weight: 700;
    }

    .aduan-page-subtitle {
        font-size: 12px;
        line-height: 1.4;
        color: #64748b;
        font-weight: 500;
    }

    .aduan-list-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        align-items: center;
    }

    .aduan-list-title {
        font-size: 12px;
        line-height: 1.3;
        color: #0f172a;
        font-weight: 700;
    }

    .aduan-filter-select,
    .aduan-filter-search,
    .aduan-btn-action {
        font-size: 12px;
    }

    .aduan-filter-select {
        width: 130px;
        min-height: 32px;
        border-radius: 8px;
        border-color: #d5dbe6;
    }

    .aduan-filter-search-wrap {
        width: 210px;
    }

    .aduan-filter-search {
        min-height: 32px;
        border-radius: 8px;
        border-color: #d5dbe6;
    }

    .aduan-btn-action {
        min-height: 32px;
    }

    .aduan-table thead th {
        font-size: 11px;
        line-height: 1.25;
        color: #64748b;
        font-weight: 600;
        padding-top: 10px;
        padding-bottom: 10px;
        white-space: nowrap;
        border-right: 1px solid #eef2f7;
    }

    .aduan-table tbody td {
        font-size: 12px;
        line-height: 1.35;
        color: #334155;
        font-weight: 500;
        padding-top: 10px;
        padding-bottom: 10px;
        vertical-align: middle;
        white-space: nowrap;
        border-right: 1px solid #eef2f7;
    }

    .aduan-table thead th:last-child,
    .aduan-table tbody td:last-child {
        border-right: 0;
    }

    .aduan-ticket {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
    }

    .aduan-asset-id {
        color: #0f172a;
        font-weight: 600;
    }

    .aduan-region,
    .aduan-date {
        color: #64748b;
    }

    .aduan-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        min-height: 24px;
        padding: 2px 8px;
        font-size: 10px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .aduan-status-menunggu {
        background: #fef9c3;
        color: #a16207;
        border: 1px solid #fde68a;
    }

    .aduan-status-diproses {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .aduan-status-ditolak {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .aduan-status-selesai {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .aduan-detail-btn {
        min-height: 28px;
        font-size: 12px;
        border-color: #dbe1eb;
        color: #64748b;
        min-width: 62px;
    }

    .aduan-pagination-wrap nav {
        margin-left: auto;
    }

    .aduan-pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        border-top: 1px solid #e9edf5;
        padding-top: 12px;
    }

    .aduan-pagination-left {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #64748b;
    }

    .aduan-per-page {
        min-height: 30px;
        min-width: 54px;
        border-radius: 8px;
        border-color: #d5dbe6;
        font-size: 12px;
        color: #475569;
        padding-right: 22px;
    }

    .aduan-pagination-right {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .aduan-pagination-summary {
        font-size: 12px;
        color: #64748b;
        white-space: nowrap;
    }

    .aduan-page-links {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .aduan-page-link,
    .aduan-page-nav {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 12px;
        color: #64748b;
        text-decoration: none;
        border: 1px solid transparent;
    }

    .aduan-page-link.is-active {
        background: #f1f5f9;
        color: #334155;
        font-weight: 700;
        border-color: #e2e8f0;
    }

    .aduan-page-nav.is-disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    .aduan-mobile-list {
        display: none;
    }

    @media (max-width: 767.98px) {
        .aduan-breadcrumb-wrap {
            margin-bottom: 10px !important;
        }

        .aduan-page-title {
            font-size: 22px;
            line-height: 1.2;
            margin-bottom: 10px !important;
        }

        .aduan-page-subtitle {
            font-size: 12px;
        }

        .aduan-list-header {
            gap: 10px;
            align-items: flex-start;
            flex-direction: column;
            padding: 14px;
        }

        .aduan-list-title {
            font-size: 14px;
        }

        .aduan-list-header .card-toolbar {
            width: 100%;
        }

        .aduan-list-header form {
            width: 100%;
            gap: 8px !important;
        }

        .aduan-filter-search-wrap {
            width: 100%;
            order: 1;
        }

        .aduan-filter-search {
            min-height: 40px;
            font-size: 12px;
        }

        .aduan-filter-select {
            width: 100%;
            order: 2;
            min-height: 40px;
            font-size: 12px;
        }

        .aduan-table {
            display: none;
        }

        .aduan-mobile-list {
            display: flex;
            flex-direction: column;
            border-top: 1px solid #e9edf5;
        }

        .aduan-mobile-item {
            border-bottom: 1px solid #e9edf5;
            padding: 10px 0;
        }

        .aduan-mobile-item:last-child {
            border-bottom: 0;
        }

        .aduan-mobile-top {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto auto;
            align-items: center;
            gap: 6px;
        }

        .aduan-mobile-date {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.2;
        }

        .aduan-mobile-type {
            font-size: 10px;
            min-height: 22px;
            padding: 2px 8px;
            border-radius: 999px;
        }

        .aduan-mobile-ticket {
            margin-top: 5px;
            font-size: 12px;
            color: #0f172a;
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
        }

        .aduan-mobile-asset {
            margin-top: 3px;
            font-size: 18px;
            line-height: 1.1;
            color: #0f172a;
            font-weight: 700;
        }

        .aduan-detail-btn {
            min-height: 26px;
            min-width: 54px;
            font-size: 11px;
            padding: 2px 10px;
        }

        .aduan-pagination-wrap {
            flex-direction: column-reverse;
            align-items: flex-start;
            gap: 10px;
        }

        .aduan-pagination-right {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@endpush
