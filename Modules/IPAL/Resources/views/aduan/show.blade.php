@extends('ipal::layouts.main')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        @php
            $statusLabelMap = [
                'masuk' => 'Menunggu',
                'verifikasi' => 'Verifikasi',
                'proses' => 'Diproses',
                'ditolak' => 'Ditolak',
                'selesai' => 'Selesai',
            ];

            $statusBadgeMap = [
                'masuk' => 'badge-light-primary',
                'verifikasi' => 'badge-light-warning',
                'proses' => 'badge-light-info',
                'ditolak' => 'badge-light-danger',
                'selesai' => 'badge-light-success',
            ];

            $currentStatus = strtolower(trim((string) $aduan->status_aduan));
            $statusAliasMap = [
                'tolak' => 'ditolak',
                'rejected' => 'ditolak',
                'diterima' => 'proses',
            ];
            $currentStatus = $statusAliasMap[$currentStatus] ?? $currentStatus;
            $aduanStatusLabel = $statusLabelMap[$currentStatus] ?? ucfirst($aduan->status_aduan);
            $aduanStatusBadge = $statusBadgeMap[$currentStatus] ?? 'badge-light';

            $asset = $aduan->pipa ?: $aduan->manhole;
            $assetTypeLabel = $aduan->pipa_id ? 'Pipa' : ($aduan->manhole_id ? 'Manhole' : '-');
            $assetCode = $aduan->pipa?->kode_pipa ?? $aduan->manhole?->kode_manhole ?? '-';
            $assetLocation = $aduan->pipa?->wilayah ?? $aduan->manhole?->wilayah ?? '-';

            $normalizeAssetStatus = static function (?string $status): string {
                $raw = strtolower(trim((string) $status));
                if ($raw === 'aman' || $raw === 'baik') {
                    return 'baik';
                }
                if ($raw === 'dalam perbaikan' || $raw === 'perbaikan') {
                    return 'perbaikan';
                }
                if (in_array($raw, ['masalah', 'bermasalah', 'rusak'], true)) {
                    return 'rusak';
                }

                return '-';
            };

            $assetStatusMap = [
                'baik' => ['label' => 'Baik', 'badge' => 'badge-light-success'],
                'perbaikan' => ['label' => 'Perbaikan', 'badge' => 'badge-light-warning'],
                'rusak' => ['label' => 'Rusak', 'badge' => 'badge-light-danger'],
                '-' => ['label' => '-', 'badge' => 'badge-light'],
            ];

            $assetStatusKey = $asset ? $normalizeAssetStatus($asset->status) : '-';
            $assetStatus = $assetStatusMap[$assetStatusKey] ?? $assetStatusMap['-'];

            $canVerify = in_array($currentStatus, ['masuk', 'verifikasi', 'ditolak'], true);
            $canStartRepair = $currentStatus === 'proses';
            $canFinish = $currentStatus === 'proses';
            $isVerificationPending = in_array($currentStatus, ['masuk', 'verifikasi'], true);
            $isVerificationRejected = $currentStatus === 'ditolak';
            $isVerificationAccepted = in_array($currentStatus, ['proses', 'selesai'], true);

            $workflowStepMap = [
                'masuk' => 1,
                'verifikasi' => 2,
                'proses' => 3,
                'selesai' => 4,
                'ditolak' => 1,
            ];
            $activeWorkflowStep = $workflowStepMap[$currentStatus] ?? 1;

            $fotoPelapor = $aduan->dokumentasi->where('tipe_pengunggah', 'pelapor');
            $fotoAdmin = $aduan->dokumentasi->where('tipe_pengunggah', 'admin');

            $oldProgressLogs = collect(preg_split('/\r\n|\r|\n/', (string) old('catatan_tindak_lanjut', '')))
                ->map(static fn (string $line): string => trim($line))
                ->filter(static fn (string $line): bool => $line !== '')
                ->values();

            $savedProgressLogs = $aduan->history
                ->filter(static fn ($history): bool => trim((string) $history->catatan_tindak_lanjut) !== '')
                ->sortByDesc('created_at')
                ->values();

            $initialProgressPayload = $oldProgressLogs->isNotEmpty()
                ? $oldProgressLogs
                : $savedProgressLogs
                    ->map(static fn ($history): string => trim((string) $history->catatan_tindak_lanjut))
                    ->values();

            $historyItems = collect([
                [
                    'time' => $aduan->created_at,
                    'point_color' => '#8B5CF6',
                    'title' => 'Laporan aduan masuk',
                    'subtitle' => 'Laporan diterima melalui aduan masyarakat',
                    'note' => null,
                ],
            ]);

            foreach ($aduan->history->sortBy('created_at')->values() as $h) {
                $fromRaw = strtolower(trim((string) $h->status_sebelumnya));
                $toRaw = strtolower(trim((string) $h->status_sesudah));
                $from = $statusAliasMap[$fromRaw] ?? $fromRaw;
                $to = $statusAliasMap[$toRaw] ?? $toRaw;
                $note = trim((string) $h->catatan_tindak_lanjut);
                $adminName = $h->admin?->name ?? 'Admin';

                $title = 'Status aduan diperbarui';
                $subtitle = 'oleh: ' . $adminName;
                $pointColor = '#3B82F6';

                if ($to === 'ditolak') {
                    $title = 'Laporan aduan ditolak';
                    $pointColor = '#F8285A';
                } elseif ($to === 'selesai') {
                    $title = 'Perbaikan selesai';
                    $pointColor = '#22C55E';
                } elseif ($to === 'proses' && in_array($from, ['masuk', 'verifikasi', 'ditolak'], true)) {
                    $title = 'Laporan aduan diterima';
                    $pointColor = '#1B84FF';
                } elseif ($from === 'proses' && $to === 'proses' && $note === '') {
                    $title = 'Proses perbaikan dimulai';
                    $pointColor = '#EAB308';
                } elseif ($note !== '') {
                    $title = 'Catatan tindakan ditambahkan';
                    $pointColor = '#EAB308';
                }

                $historyItems->push([
                    'time' => $h->created_at,
                    'point_color' => $pointColor,
                    'title' => $title,
                    'subtitle' => $subtitle,
                    'note' => $note !== '' ? $note : null,
                ]);
            }
        @endphp

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
            <a href="{{ route('ipal.aduan.index') }}" class="btn btn-sm btn-light">
                <i class="ki-outline ki-arrow-left fs-5 me-1"></i>Kembali ke Daftar
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted fs-7">Status Aduan:</span>
                <span class="badge fs-7 {{ $aduanStatusBadge }}">{{ $aduanStatusLabel }}</span>
            </div>
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
            <div class="col-xxl-7">
                <div class="card mb-5 border border-slate-200 rounded-xl shadow-[0_10px_28px_rgba(15,23,42,0.04)]">
                    <div class="card-header border-0 pt-5 pb-3 min-h-0">
                        <h3 class="card-title fw-bold">Informasi Aduan</h3>
                    </div>
                    <div class="card-body pt-0">
                        <table class="table table-borderless fs-6 mb-0">
                            <tr>
                                <td class="text-muted fw-semibold w-170px py-2">Nomor Tiket</td>
                                <td class="fw-bold font-monospace py-2">{{ $aduan->nomor_tiket }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2">Tanggal Masuk</td>
                                <td class="py-2">{{ $aduan->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2">Aset</td>
                                <td class="py-2">
                                    <span class="badge {{ $aduan->pipa_id ? 'badge-light-info' : 'badge-light-warning' }}">{{ $assetTypeLabel }}</span>
                                    <span class="ms-2 fw-semibold">{{ $assetCode }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2">Status Aset</td>
                                <td class="py-2">
                                    <span class="badge {{ $assetStatus['badge'] }}">{{ $assetStatus['label'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2">Lokasi</td>
                                <td class="py-2">{{ $assetLocation }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold py-2 align-top">Deskripsi Aduan</td>
                                <td class="py-2">{{ $aduan->deskripsi }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mb-5 border border-slate-200 rounded-xl shadow-[0_10px_28px_rgba(15,23,42,0.04)]">
                    <div class="card-header border-0 pt-5 pb-3 min-h-0">
                        <h3 class="card-title fw-bold">Dokumentasi Pelapor</h3>
                        <span class="text-muted fs-7">{{ $fotoPelapor->count() }} foto</span>
                    </div>
                    <div class="card-body pt-0">
                        @if($fotoPelapor->isNotEmpty())
                            <div class="grid grid-cols-3 gap-2.5 sm:grid-cols-4 lg:[grid-template-columns:repeat(auto-fill,minmax(120px,1fr))]">
                                @foreach($fotoPelapor as $foto)
                                    <a href="{{ Storage::url($foto->file_path) }}" target="_blank">
                                        <img src="{{ Storage::url($foto->file_path) }}" alt="{{ $foto->file_name }}" class="w-full h-[110px] object-cover rounded-[10px] border border-slate-200 transition-transform duration-200 hover:-translate-y-0.5">
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted fs-7 py-2">Belum ada dokumentasi dari pelapor.</div>
                        @endif
                    </div>
                </div>

                <div class="card border border-slate-200 rounded-xl shadow-[0_10px_28px_rgba(15,23,42,0.04)]">
                    <div class="card-header border-0 pt-5 pb-3 min-h-0">
                        <h3 class="card-title fw-bold">Dokumentasi Tindak Lanjut</h3>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fs-7">{{ $fotoAdmin->count() }} foto</span>
                            <button type="button" id="followUpUploadBtn" class="btn btn-sm btn-primary">Upload Foto</button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <input id="followUpPhotoInput" type="file" name="foto" form="workflowForm" accept="image/jpeg,image/jpg,image/png,image/webp" class="d-none">
                        <div id="followUpUploadInfo" class="text-muted fs-8 mb-3"></div>

                        @if($fotoAdmin->isNotEmpty())
                            <div class="grid grid-cols-3 gap-2.5 sm:grid-cols-4 lg:[grid-template-columns:repeat(auto-fill,minmax(120px,1fr))]">
                                @foreach($fotoAdmin as $foto)
                                    <a href="{{ Storage::url($foto->file_path) }}" target="_blank">
                                        <img src="{{ Storage::url($foto->file_path) }}" alt="{{ $foto->file_name }}" class="w-full h-[110px] object-cover rounded-[10px] border border-slate-200 transition-transform duration-200 hover:-translate-y-0.5">
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted fs-7 py-2">Belum ada dokumentasi tindak lanjut.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xxl-5">
                <div class="card mb-5 border border-slate-200 rounded-xl shadow-[0_10px_28px_rgba(15,23,42,0.04)]">
                    <div class="border-b border-slate-200 pt-5 pb-3 min-h-0 d-flex align-items-center gap-3 px-9">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.8562 21.4998H6.64382C4.89843 21.5131 3.21897 20.8342 1.97357 19.6119C0.728177 18.3895 0.0184614 16.7235 0 14.979V7.32971C0.00522825 6.46335 0.181299 5.60652 0.51814 4.80824C0.85498 4.00996 1.34598 3.28589 1.96305 2.67746C2.58012 2.06903 3.31116 1.58817 4.11433 1.2624C4.9175 0.936623 5.77706 0.772327 6.64382 0.778906H10.1805C10.2789 0.778906 10.3764 0.798278 10.4673 0.835916C10.5582 0.873555 10.6408 0.928722 10.7104 0.998268C10.7799 1.06781 10.8351 1.15038 10.8728 1.24124C10.9105 1.33211 10.9298 1.4295 10.9298 1.52785C10.9298 1.62621 10.9105 1.7236 10.8728 1.81446C10.8351 1.90533 10.7799 1.98789 10.7104 2.05744C10.6408 2.12699 10.5582 2.18215 10.4673 2.21979C10.3764 2.25743 10.2789 2.2768 10.1805 2.2768H6.64382C5.30538 2.26871 4.01771 2.78837 3.06011 3.72308C2.10252 4.65779 1.55222 5.93217 1.52858 7.26979V14.919C1.55222 16.2567 2.10252 17.531 3.06011 18.4658C4.01771 19.4005 5.30538 19.9201 6.64382 19.912H14.8562C16.1946 19.9201 17.4823 19.4005 18.4399 18.4658C19.3975 17.531 19.9478 16.2567 19.9714 14.919V9.90609C19.9929 9.71853 20.0827 9.54543 20.2236 9.41977C20.3646 9.2941 20.5468 9.22465 20.7357 9.22465C20.9246 9.22465 21.1069 9.2941 21.2478 9.41977C21.3887 9.54543 21.4785 9.71853 21.5 9.90609V14.979C21.4815 16.7235 20.7718 18.3895 19.5264 19.6119C18.281 20.8342 16.6016 21.5131 14.8562 21.4998ZM16.4247 1.52785C16.2794 1.52507 16.1355 1.55717 16.0051 1.62146C15.8747 1.68575 15.7617 1.78035 15.6754 1.89734L15.086 2.65627C15.2846 3.46333 15.7668 4.17215 16.4447 4.65346C17.0719 5.12817 17.8359 5.38739 18.6227 5.39243H18.7426L19.332 4.63349C19.4056 4.5379 19.4594 4.42862 19.4902 4.31204C19.5211 4.19546 19.5285 4.0739 19.5118 3.95445C19.4973 3.83496 19.4577 3.71987 19.3957 3.61665C19.3338 3.51343 19.2508 3.42439 19.1522 3.35529L16.9842 1.71759C16.824 1.59385 16.6271 1.52708 16.4247 1.52785ZM16.4247 0.0299579C16.9574 0.0299506 17.4759 0.201536 17.9033 0.519271L20.0913 2.137C20.3486 2.32517 20.5656 2.56294 20.7295 2.83628C20.8934 3.10962 21.0008 3.413 21.0455 3.72851C21.0902 4.04403 21.0712 4.3653 20.9897 4.67337C20.9082 4.98144 20.7658 5.27007 20.5709 5.52224L19.9115 6.38104C19.8146 6.50909 19.6931 6.61655 19.5541 6.69713C19.4152 6.77772 19.2616 6.82982 19.1022 6.85038H18.6227C17.5072 6.85557 16.419 6.50583 15.5156 5.85178C14.4828 5.10869 13.7691 4.00296 13.5174 2.75613C13.4937 2.5979 13.5027 2.43648 13.544 2.28188C13.5852 2.12729 13.6578 1.9828 13.7572 1.85739L14.4166 0.998598C14.6492 0.688543 14.9509 0.436888 15.2977 0.263562C15.6446 0.090236 16.027 0 16.4147 0L16.4247 0.0299579ZM11.869 6.82042L9.63104 9.76628C9.53297 9.88853 9.47692 10.0391 9.47119 10.1957V13.3512C9.46638 13.3901 9.47115 13.4295 9.48508 13.466C9.49901 13.5026 9.52168 13.5352 9.55112 13.561C9.61218 13.6223 9.69437 13.658 9.7809 13.6608H9.87082L12.928 12.7621C13.0788 12.7124 13.2111 12.6185 13.3076 12.4925L15.5455 9.59652C14.8345 9.31036 14.163 8.9345 13.5474 8.47809C12.9289 8.01773 12.3745 7.47706 11.8989 6.87035M11.9489 4.71338C12.0623 4.71072 12.1741 4.74027 12.2714 4.7986C12.3686 4.85693 12.4474 4.94165 12.4984 5.04292C12.9997 5.92062 13.6806 6.68282 14.4965 7.27978C15.3085 7.8859 16.2425 8.3084 17.234 8.51804C17.3413 8.5346 17.4423 8.57955 17.5265 8.64823C17.6106 8.71691 17.6749 8.80683 17.7126 8.90868C17.7503 9.01053 17.7601 9.12059 17.7409 9.22749C17.7218 9.33438 17.6744 9.43421 17.6036 9.51663L14.6064 13.4311C14.313 13.8142 13.9026 14.0912 13.4375 14.22L10.3504 15.1188C10.1818 15.1696 10.0069 15.1965 9.83085 15.1987C9.58918 15.2027 9.34916 15.1582 9.12496 15.0679C8.90077 14.9776 8.69696 14.8434 8.52559 14.673C8.35421 14.5026 8.21874 14.2996 8.12719 14.0761C8.03564 13.8525 7.98986 13.6128 7.99256 13.3712V10.2157C8.00044 9.73673 8.16541 9.27363 8.46213 8.8975L11.4593 4.983C11.5198 4.9087 11.5959 4.84868 11.6823 4.80724C11.7686 4.76581 11.8631 4.74399 11.9589 4.74334L11.9489 4.71338Z" fill="#2B7FFF"/>
                        </svg>
                        <h3 class="wf-heading mb-0">Workflow Penanganan</h3>
                    </div>
                    <div class="card-body pt-3">
                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0 ps-4">
                                    @foreach($errors->all() as $err)
                                        <li class="fs-7">{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="workflowForm" action="{{ route('ipal.aduan.updateStatus', $aduan->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="wf-timeline">
                                <div class="wf-step {{ $activeWorkflowStep === 1 ? 'wf-step-active' : '' }}">
                                    <div class="wf-step-num">1</div>
                                    <div class="wf-step-card">
                                        <h4 class="wf-step-title">Verifikasi Laporan</h4>
                                        <div class="wf-step-row wf-verify-row" id="verifyActionRow">
                                            @if($isVerificationPending)
                                                <div class="wf-verify-actions" id="verifyActionButtons">
                                                    <button type="submit" name="workflow_action" value="terima" id="verifyAcceptBtn" class="wf-btn wf-btn-primary wf-btn-equal gap-2" @disabled(!$canVerify)>
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g clip-path="url(#clip0_907_6229)">
                                                            <path d="M9.28602 1.54591C9.70198 1.54591 10.1139 1.62784 10.4982 1.78703C10.8825 1.94621 11.2316 2.17952 11.5258 2.47365C11.8199 2.76778 12.0532 3.11697 12.2124 3.50127C12.3716 3.88556 12.4535 4.29745 12.4535 4.71341V9.28675C12.4535 9.70271 12.3716 10.1146 12.2124 10.4989C12.0532 10.8832 11.8199 11.2324 11.5258 11.5265C11.2316 11.8206 10.8825 12.054 10.4982 12.2131C10.1139 12.3723 9.70198 12.4542 9.28602 12.4542H4.71268C3.87261 12.4542 3.06694 12.1205 2.47292 11.5265C1.8789 10.9325 1.54518 10.1268 1.54518 9.28675V4.71341C1.54518 3.87334 1.8789 3.06768 2.47292 2.47365C3.06694 1.87963 3.87261 1.54591 4.71268 1.54591H9.28602ZM6.45685 7.86341L4.66602 6.06675C4.5614 5.96872 4.42434 5.91265 4.28102 5.90925C4.17336 5.91031 4.06839 5.94305 3.97921 6.00337C3.89003 6.06369 3.82059 6.14893 3.77953 6.24846C3.73847 6.34799 3.72763 6.4574 3.74834 6.56305C3.76906 6.66871 3.82042 6.76592 3.89602 6.84258L6.07185 9.02425C6.17868 9.11937 6.31673 9.17192 6.45977 9.17192C6.6028 9.17192 6.74086 9.11937 6.84768 9.02425L10.1143 5.75175C10.1899 5.67509 10.2413 5.57787 10.262 5.47222C10.2827 5.36657 10.2719 5.25715 10.2308 5.15763C10.1898 5.0581 10.1203 4.97286 10.0312 4.91253C9.94197 4.85221 9.83701 4.81948 9.72935 4.81842C9.58602 4.82182 9.44897 4.87789 9.34435 4.97591L6.46268 7.89258M9.28602 0.729248H4.71268C3.65602 0.729248 2.64263 1.14901 1.89545 1.89618C1.14827 2.64336 0.728516 3.65675 0.728516 4.71341V9.28675C0.728516 10.3434 1.14827 11.3568 1.89545 12.104C2.64263 12.8512 3.65602 13.2709 4.71268 13.2709H9.28602C10.3427 13.2709 11.3561 12.8512 12.1032 12.104C12.8504 11.3568 13.2702 10.3434 13.2702 9.28675V4.71341C13.2702 3.65675 12.8504 2.64336 12.1032 1.89618C11.3561 1.14901 10.3427 0.729248 9.28602 0.729248Z" fill="white"/>
                                                            </g>
                                                            <defs>
                                                            <clipPath id="clip0_907_6229">
                                                            <rect width="14" height="14" fill="white"/>
                                                            </clipPath>
                                                            </defs>
                                                            </svg>
                                                        Terima</button>
                                                    <button type="submit" name="workflow_action" value="tolak" id="verifyRejectBtn" class="wf-btn wf-btn-danger wf-btn-equal gap-2" @disabled(!$canVerify)>
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_907_6229)">
                                                        <path d="M9.28602 1.54591C9.70198 1.54591 10.1139 1.62784 10.4982 1.78703C10.8825 1.94621 11.2316 2.17952 11.5258 2.47365C11.8199 2.76778 12.0532 3.11697 12.2124 3.50127C12.3716 3.88556 12.4535 4.29745 12.4535 4.71341V9.28675C12.4535 9.70271 12.3716 10.1146 12.2124 10.4989C12.0532 10.8832 11.8199 11.2324 11.5258 11.5265C11.2316 11.8206 10.8825 12.054 10.4982 12.2131C10.1139 12.3723 9.70198 12.4542 9.28602 12.4542H4.71268C3.87261 12.4542 3.06694 12.1205 2.47292 11.5265C1.8789 10.9325 1.54518 10.1268 1.54518 9.28675V4.71341C1.54518 3.87334 1.8789 3.06768 2.47292 2.47365C3.06694 1.87963 3.87261 1.54591 4.71268 1.54591H9.28602ZM6.45685 7.86341L4.66602 6.06675C4.5614 5.96872 4.42434 5.91265 4.28102 5.90925C4.17336 5.91031 4.06839 5.94305 3.97921 6.00337C3.89003 6.06369 3.82059 6.14893 3.77953 6.24846C3.73847 6.34799 3.72763 6.4574 3.74834 6.56305C3.76906 6.66871 3.82042 6.76592 3.89602 6.84258L6.07185 9.02425C6.17868 9.11937 6.31673 9.17192 6.45977 9.17192C6.6028 9.17192 6.74086 9.11937 6.84768 9.02425L10.1143 5.75175C10.1899 5.67509 10.2413 5.57787 10.262 5.47222C10.2827 5.36657 10.2719 5.25715 10.2308 5.15763C10.1898 5.0581 10.1203 4.97286 10.0312 4.91253C9.94197 4.85221 9.83701 4.81948 9.72935 4.81842C9.58602 4.82182 9.44897 4.87789 9.34435 4.97591L6.46268 7.89258M9.28602 0.729248H4.71268C3.65602 0.729248 2.64263 1.14901 1.89545 1.89618C1.14827 2.64336 0.728516 3.65675 0.728516 4.71341V9.28675C0.728516 10.3434 1.14827 11.3568 1.89545 12.104C2.64263 12.8512 3.65602 13.2709 4.71268 13.2709H9.28602C10.3427 13.2709 11.3561 12.8512 12.1032 12.104C12.8504 11.3568 13.2702 10.3434 13.2702 9.28675V4.71341C13.2702 3.65675 12.8504 2.64336 12.1032 1.89618C11.3561 1.14901 10.3427 0.729248 9.28602 0.729248Z" fill="white"/>
                                                        </g>
                                                        <defs>
                                                        <clipPath id="clip0_907_6229">
                                                        <rect width="14" height="14" fill="white"/>
                                                        </clipPath>
                                                        </defs>
                                                        </svg>
                                                    Tolak</button>
                                                </div>
                                                <span id="verifyInfoText" class="wf-meta-text d-none">Diverifikasi oleh: {{ auth()->user()->name ?? 'Admin' }}</span>
                                            @elseif($isVerificationRejected)
                                                <div class="wf-verify-actions wf-verify-actions-single wf-verify-actions-status" id="verifyActionButtons">
                                                    <button type="button" class="wf-btn wf-btn-danger wf-btn-equal wf-btn-static" disabled>Tolak</button>
                                                </div>
                                                <span id="verifyInfoText" class="wf-meta-text d-none">Diverifikasi oleh: {{ auth()->user()->name ?? 'Admin' }}</span>
                                            @elseif($isVerificationAccepted)
                                                <div class="wf-verify-actions wf-verify-actions-single wf-verify-actions-status" id="verifyActionButtons">
                                                    <button type="button" class="wf-btn wf-btn-primary wf-btn-equal wf-btn-static" disabled>Terima</button>
                                                </div>
                                                <span id="verifyInfoText" class="wf-meta-text">Diverifikasi oleh: {{ auth()->user()->name ?? 'Admin' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="wf-step {{ $activeWorkflowStep === 2 ? 'wf-step-active' : '' }}">
                                    <div class="wf-step-num">2</div>
                                    <div class="wf-step-card">
                                        <h4 class="wf-step-title">Tindakan Awal</h4>
                                        <p class="wf-step-desc">Analisis awal menunjukkan perlu penggantian joint pipa 4 inch.</p>
                                    </div>
                                </div>

                                <div class="wf-step {{ $activeWorkflowStep === 3 ? 'wf-step-active' : '' }}">
                                    <div class="wf-step-num">3</div>
                                    <div class="wf-step-card">
                                        <h4 class="wf-step-title">Eksekusi Perbaikan</h4>
                                        <p class="wf-step-desc">Mulai proses perbaikan fisik dan pencatatan progres di lokasi.</p>

                                        <button type="submit" name="workflow_action" value="mulai_perbaikan" class="wf-btn wf-btn-warning gap-2" @disabled(!$canStartRepair)>
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7.78361 2.88549C7.67672 2.99453 7.61686 3.14113 7.61686 3.29382C7.61686 3.44651 7.67672 3.59311 7.78361 3.70215L8.71694 4.63549C8.82598 4.74237 8.97258 4.80224 9.12527 4.80224C9.27796 4.80224 9.42456 4.74237 9.53361 4.63549L11.3454 2.82424C11.5321 2.6364 11.8489 2.6959 11.9189 2.9514C12.0951 3.59241 12.0851 4.27036 11.8901 4.90592C11.6951 5.54147 11.3232 6.10835 10.8177 6.54021C10.3123 6.97207 9.69436 7.25106 9.03617 7.34453C8.37797 7.43799 7.70677 7.34207 7.10111 7.06799L2.48694 11.6822C2.25487 11.9141 1.94016 12.0444 1.61202 12.0444C1.28389 12.0443 0.969218 11.9139 0.737231 11.6819C0.505244 11.4498 0.374945 11.1351 0.375 10.8069C0.375055 10.4788 0.505458 10.1641 0.737522 9.93215L5.35169 5.31799C5.07761 4.71232 4.98168 4.04112 5.07515 3.38292C5.16862 2.72473 5.4476 2.10677 5.87947 1.60135C6.31133 1.09592 6.8782 0.723952 7.51376 0.528952C8.14932 0.333952 8.82726 0.323992 9.46827 0.500237C9.72377 0.570237 9.78327 0.886403 9.59602 1.07424L7.78361 2.88549Z" stroke="white" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Mulai Perbaikan</button>
                                        @if(!$canStartRepair)
                                            <div class="text-muted fs-8 mt-2">Aksi ini aktif saat aduan berstatus Diproses.</div>
                                        @endif

                                        <div class="wf-progress-title">CATATAN PROGRESS</div>
                                        <div id="progressLogList" class="wf-progress-list">
                                            @foreach($savedProgressLogs as $savedLog)
                                                <div class="wf-progress-item" data-log-text="{{ $savedLog->catatan_tindak_lanjut }}">
                                                    <div class="wf-progress-text">{{ $savedLog->catatan_tindak_lanjut }}</div>
                                                    <div class="wf-progress-meta">{{ $savedLog->created_at?->format('d M Y, H:i') }} - {{ $savedLog->admin?->name ?? 'Admin' }}</div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <input type="text" id="progressLogInput" class="wf-input" placeholder="Tulis catatan progress lalu tekan Enter">
                                        <button type="button" id="progressAddBtn" class="wf-manual-btn">Tambah Catatan Manual</button>
                                        <textarea name="catatan_tindak_lanjut" id="progressLogPayload" class="d-none">{{ $initialProgressPayload->implode("\n") }}</textarea>
                                        <button type="submit" id="progressAutoSaveSubmit" name="workflow_action" value="simpan_catatan" class="d-none" tabindex="-1" aria-hidden="true"></button>
                                    </div>
                                </div>

                                <div class="wf-step wf-step-last {{ $activeWorkflowStep === 4 ? 'wf-step-active' : '' }}">
                                    <div class="wf-step-num">4</div>
                                    <div class="wf-step-card">
                                        <h4 class="wf-step-title">Selesai</h4>
                                        <p class="wf-step-desc">Tutup laporan dan verifikasi akhir.</p>
                                        <button type="submit" name="workflow_action" value="tandai_selesai" class="wf-btn wf-btn-success gap-2" @disabled(!$canFinish)>
                                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.13125 8.4921C5.69018 8.4921 5.25902 8.3613 4.89228 8.11626C4.52555 7.87121 4.23971 7.52292 4.07092 7.11543C3.90213 6.70793 3.85797 6.25953 3.94402 5.82694C4.03007 5.39435 4.24246 4.99698 4.55434 4.6851C4.86623 4.37321 5.26359 4.16082 5.69619 4.07477C6.12878 3.98872 6.57718 4.03289 6.98467 4.20168C7.39217 4.37047 7.74046 4.6563 7.9855 5.02304C8.23055 5.38977 8.36134 5.82094 8.36134 6.26201C8.36134 6.55487 8.30366 6.84486 8.19159 7.11543C8.07951 7.38599 7.91525 7.63184 7.70816 7.83892C7.50108 8.046 7.25524 8.21027 6.98467 8.32234C6.71411 8.43441 6.42411 8.4921 6.13125 8.4921ZM6.13125 4.84665C5.85132 4.84665 5.57768 4.92966 5.34492 5.08518C5.11217 5.2407 4.93076 5.46175 4.82363 5.72037C4.7165 5.979 4.68848 6.26358 4.74309 6.53813C4.7977 6.81269 4.9325 7.06488 5.13044 7.26282C5.32838 7.46076 5.58058 7.59556 5.85513 7.65018C6.12968 7.70479 6.41427 7.67676 6.67289 7.56963C6.93151 7.46251 7.15256 7.2811 7.30808 7.04834C7.46361 6.81559 7.54662 6.54194 7.54662 6.26201C7.54662 5.88663 7.3975 5.52663 7.13207 5.2612C6.86663 4.99576 6.50663 4.84665 6.13125 4.84665ZM6.13125 12.8333C4.57911 12.8333 3.36 11.9948 3.36 10.9244C3.36 9.85394 4.57911 9.01542 6.13125 9.01542C7.6834 9.01542 8.90251 9.85394 8.90251 10.9244C8.90251 11.9948 7.6834 12.8333 6.13125 12.8333ZM6.13125 9.85988C4.9954 9.85988 4.20446 10.4189 4.20446 10.9244C4.20446 11.4299 4.9954 11.9889 6.13125 11.9889C7.26711 11.9889 8.05805 11.4239 8.05805 10.9244C8.05805 10.4248 7.26711 9.85988 6.13125 9.85988ZM9.1047 4.03192C8.70676 4.0284 8.31875 3.90718 7.98958 3.68352C7.6604 3.45987 7.40479 3.1438 7.25493 2.77513C7.10508 2.40646 7.06769 2.00168 7.14748 1.6118C7.22727 1.22191 7.42067 0.864368 7.70331 0.584207C7.98595 0.304047 8.34519 0.113806 8.73576 0.0374529C9.12633 -0.0388998 9.53076 0.00205195 9.8981 0.155148C10.2654 0.308245 10.5792 0.566637 10.8 0.897768C11.0207 1.2289 11.1385 1.61796 11.1385 2.01592C11.1393 2.28117 11.0878 2.54396 10.9868 2.78924C10.8858 3.03452 10.7375 3.25746 10.5502 3.44529C10.3629 3.63313 10.1404 3.78216 9.89541 3.88384C9.65043 3.98553 9.38779 4.03787 9.12254 4.03787L9.1047 4.03192ZM9.1047 0.844382C8.86947 0.844382 8.63951 0.914138 8.44392 1.04483C8.24833 1.17552 8.09588 1.36128 8.00586 1.57861C7.91584 1.79594 7.89229 2.03508 7.93818 2.2658C7.98407 2.49652 8.09735 2.70844 8.26369 2.87478C8.43002 3.04112 8.64195 3.1544 8.87267 3.20029C9.10339 3.24618 9.34253 3.22263 9.55986 3.13261C9.77719 3.04258 9.96295 2.89014 10.0936 2.69455C10.2243 2.49895 10.2941 2.269 10.2941 2.03376C10.2941 1.72139 10.1713 1.42153 9.95205 1.19899C9.73285 0.976442 9.43489 0.849068 9.12254 0.844382H9.1047ZM3.12212 4.03787C2.72365 4.03787 2.33411 3.91978 2.00271 3.69852C1.67131 3.47726 1.41292 3.16275 1.26016 2.79472C1.1074 2.4267 1.06713 2.02165 1.14444 1.63075C1.22174 1.23985 1.41316 0.88062 1.69451 0.598442C1.97585 0.316264 2.33452 0.123792 2.72519 0.0453336C3.11586 -0.0331244 3.52102 0.00595154 3.8895 0.157626C4.25798 0.309301 4.57324 0.566771 4.79548 0.897517C5.01771 1.22826 5.13695 1.61745 5.13812 2.01592C5.1389 2.28117 5.08734 2.54396 4.98637 2.78924C4.88541 3.03452 4.73704 3.25746 4.54976 3.44529C4.36248 3.63313 4.13997 3.78216 3.89499 3.88384C3.65001 3.98553 3.38737 4.03787 3.12212 4.03787ZM3.12212 0.850329C2.88689 0.850329 2.65693 0.920085 2.46134 1.05078C2.26575 1.18147 2.1133 1.36722 2.02328 1.58455C1.93326 1.80188 1.9097 2.04103 1.9556 2.27175C2.00149 2.50246 2.11477 2.71439 2.2811 2.88073C2.44744 3.04707 2.65937 3.16034 2.89009 3.20624C3.1208 3.25213 3.35995 3.22857 3.57728 3.13855C3.79461 3.04853 3.98037 2.89609 4.11106 2.70049C4.24175 2.5049 4.3115 2.27495 4.3115 2.03971C4.31229 1.88302 4.2821 1.72772 4.22268 1.58273C4.16326 1.43774 4.07577 1.30592 3.96525 1.19484C3.85473 1.08377 3.72335 0.995627 3.57866 0.935484C3.43397 0.87534 3.27882 0.84438 3.12212 0.844382V0.850329ZM12.0425 6.38095C12.0425 5.28077 10.7163 4.41847 9.02739 4.41847C8.96733 4.41135 8.90645 4.41704 8.84875 4.43516C8.79104 4.45328 8.73783 4.48342 8.69262 4.52359C8.64741 4.56376 8.61122 4.61306 8.58644 4.66823C8.56166 4.7234 8.54884 4.78319 8.54884 4.84367C8.54884 4.90415 8.56166 4.96395 8.58644 5.01912C8.61122 5.07429 8.64741 5.12358 8.69262 5.16375C8.73783 5.20393 8.79104 5.23406 8.84875 5.25218C8.90645 5.2703 8.96733 5.27599 9.02739 5.26888C10.3 5.26888 11.1921 5.86357 11.1921 6.38095C11.1921 6.89833 10.3 7.49896 9.02739 7.49896C8.91541 7.49896 8.80802 7.54345 8.72883 7.62263C8.64965 7.70182 8.60516 7.80921 8.60516 7.92119C8.60516 8.03318 8.64965 8.14057 8.72883 8.21975C8.80802 8.29894 8.91541 8.34342 9.02739 8.34342C10.7223 8.33748 12.0484 7.48112 12.0484 6.375L12.0425 6.38095ZM3.44326 7.82604C3.44326 7.77009 3.43215 7.7147 3.41056 7.66308C3.38897 7.61146 3.35733 7.56465 3.31749 7.52536C3.27765 7.48607 3.2304 7.4551 3.17849 7.43423C3.12657 7.41336 3.07103 7.40303 3.01508 7.40381C1.74244 7.40381 0.850407 6.80912 0.850407 6.2858C0.850407 5.76247 1.74244 5.17373 3.01508 5.17373C3.07514 5.18084 3.13603 5.17515 3.19373 5.15703C3.25143 5.13891 3.30464 5.10878 3.34985 5.0686C3.39506 5.02843 3.43125 4.97914 3.45603 4.92397C3.48082 4.8688 3.49363 4.809 3.49363 4.74852C3.49363 4.68804 3.48082 4.62825 3.45603 4.57308C3.43125 4.51791 3.39506 4.46861 3.34985 4.42844C3.30464 4.38827 3.25143 4.35813 3.19373 4.34001C3.13603 4.32189 3.07514 4.3162 3.01508 4.32332C1.32616 4.32332 0 5.18562 0 6.2858C0 7.38597 1.32616 8.24827 3.01508 8.24827C3.07103 8.24906 3.12657 8.23872 3.17849 8.21786C3.2304 8.19699 3.27765 8.16601 3.31749 8.12673C3.35733 8.08744 3.38897 8.04063 3.41056 7.98901C3.43215 7.93739 3.44326 7.882 3.44326 7.82604Z" fill="white"/>
                                            </svg>
                                            Tandai Selesai</button>
                                        @if(!$canFinish)
                                            <div class="text-muted fs-8 mt-2">Aksi ini aktif saat aduan berstatus Diproses.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border border-slate-200 rounded-xl shadow-[0_10px_28px_rgba(15,23,42,0.04)]">
                    <div class="border-b border-slate-200 pt-5 pb-3 min-h-0 d-flex align-items-center gap-3 px-9">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.8067 3.22667C15.9618 1.3818 13.4597 0.345215 10.85 0.345215C8.24028 0.345215 5.73823 1.3818 3.89336 3.22667C2.04849 5.07153 1.0119 7.57359 1.0119 10.1833H2.49869C2.49869 7.96797 3.37871 5.84338 4.94523 4.27686C6.51174 2.71035 8.63633 1.83032 10.8517 1.83032C13.067 1.83032 15.1916 2.71035 16.7581 4.27686C18.3246 5.84338 19.2047 7.96797 19.2047 10.1833C19.2047 12.3987 18.3246 14.5233 16.7581 16.0898C15.1916 17.6563 13.067 18.5364 10.8517 18.5364C9.04675 18.5378 7.29317 17.9405 5.86458 16.8378C4.43599 15.7352 3.41402 14.1883 2.95865 12.4418H4.92459L2.26995 8.73086L0.0202637 12.7136H1.41872C1.89084 14.8554 3.08011 16.7721 4.78873 18.1455C6.49735 19.5189 8.62404 20.2654 10.8166 20.2602C13.4282 20.2602 15.9329 19.2245 17.7808 17.3804C19.6288 15.5364 20.6697 13.0338 20.6756 10.4222C20.6816 7.81063 19.652 5.30333 17.8124 3.45092L17.8067 3.22667ZM10.1083 5.33759V10.7436L14.3065 13.2209L15.0499 11.9586L11.5951 9.92183V5.33759H10.1083Z" fill="#1B84FF"/>
                        </svg>
                        <h3 class="wf-heading mb-0">Riwayat Perubahan Status</h3>
                    </div>
                    <div class="card-body pt-4 pb-5 px-6 px-lg-9">
                        <div class="status-history">
                            @foreach($historyItems as $index => $item)
                                @php
                                    $eventTime = \Carbon\Carbon::parse($item['time']);
                                    $isLast = $loop->last;
                                @endphp
                                <div class="status-history-item {{ $isLast ? 'is-last' : '' }}">
                                    <div class="status-history-time">
                                        <div class="status-history-date">{{ $eventTime->translatedFormat('d F Y') }}</div>
                                        <div class="status-history-clock">{{ $eventTime->format('H:i') }}</div>
                                    </div>
                                    <div class="status-history-axis">
                                        <span class="status-history-dot" style="--dot-color: {{ $item['point_color'] }}"></span>
                                        @if(!$isLast)
                                            <span class="status-history-line"></span>
                                        @endif
                                    </div>
                                    <div class="status-history-content">
                                        <h4 class="status-history-title">{{ $item['title'] }}</h4>
                                        <div class="status-history-subtitle">{{ $item['subtitle'] }}</div>
                                        @if($item['note'])
                                            <div class="status-history-note">"{{ $item['note'] }}"</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
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
    .wf-heading {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .wf-timeline {
        position: relative;
        padding-left: 0;
    }

    .wf-step {
        position: relative;
        display: grid;
        grid-template-columns: 40px 1fr;
        column-gap: 14px;
        align-items: start;
        margin-bottom: 14px;
    }

    .wf-step::before {
        content: '';
        position: absolute;
        left: 16px;
        top: 26px;
        bottom: -40px;
        width: 2px;
        background: #d8dee8;
        transform: translateX(-50%);
        z-index: 0;
    }

    .wf-step-last {
        margin-bottom: 0;
    }

    .wf-step-last::before {
        content: none;
    }

    .wf-step-num {
        position: relative;
        z-index: 1;
        width: 32px;
        height: 32px;
        border-radius: 9999px;
        border: 1px solid #cfd7e4;
        background: #f1f5f9;
        color: #6b7280;
        font-size: 15px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
    }

    .wf-step-active .wf-step-num {
        background: #7083a5;
        border-color: #7083a5;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(73, 94, 130, 0.2);
    }

    .wf-step-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        padding: 14px 14px 12px;
    }

    .wf-step-title {
        margin: 0;
        font-size: 16px;
        line-height: 1.25;
        font-weight: 700;
        color: #0f172a;
    }

    .wf-step-desc {
        margin: 4px 0 0;
        font-size: 12px;
        line-height: 1.4;
        color: #64748b;
    }

    .wf-step-row {
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .wf-verify-row {
        flex-direction: row;
        align-items: stretch;
        gap: 8px;
    }

    .wf-verify-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        width: 100%;
    }

    .wf-verify-actions-single {
        grid-template-columns: minmax(0, 1fr);
        width: 40%;
    }

    .wf-verify-actions-status {
        grid-template-columns: minmax(0, 100%);
    }

    .wf-meta-text {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        justify-self: center;
        align-self: center;
    }

    .wf-btn {
        width: 100%;
        min-height: 35px;
        border: 0;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        transition: filter 0.2s ease;
    }

    .wf-btn:hover:not(:disabled) {
        filter: brightness(0.96);
    }

    .wf-btn:disabled {
        cursor: not-allowed;
        opacity: 0.55;
    }

    .wf-btn-primary {
        background: #1B84FF;
        max-width: 178px;
    }

    .wf-btn-danger {
        background: #F8285A;
        max-width: 128px;
    }

    .wf-btn-equal {
        max-width: none;
    }

    .wf-btn-static:disabled {
        opacity: 1;
        cursor: default;
    }

    .wf-btn-warning {
        background: #edc85f;
    }

    .wf-btn-success {
        background: #22c55e;
    }

    .wf-progress-title {
        margin-top: 12px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.03em;
        color: #64748b;
    }

    .wf-progress-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 10px;
    }

    .wf-progress-item {
        border-radius: 10px;
        border: 1px solid #dce3ee;
        background: #fff;
        padding: 11px 12px;
    }

    .wf-progress-text {
        font-size: 12px;
        line-height: 1.35;
        font-weight: 600;
        color: #1e293b;
    }

    .wf-progress-meta {
        margin-top: 4px;
        font-size: 12px;
        line-height: 1.3;
        color: #64748b;
    }

    .wf-input {
        width: 100%;
        border: 1px solid #9fb0cc;
        border-radius: 8px;
        padding: 9px 10px;
        font-size: 12px;
        color: #334155;
        background: #ffffff;
        outline: none;
    }

    .wf-input:focus {
        border-color: #7b95c7;
        box-shadow: 0 0 0 2px rgba(123, 149, 199, 0.2);
    }

    .wf-manual-btn {
        margin-top: 8px;
        width: 100%;
        min-height: 38px;
        border-radius: 8px;
        border: 2px dashed #9cb2d1;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .wf-manual-btn:hover {
        background: #eef2f8;
    }

    .status-history {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .status-history-item {
        display: grid;
        grid-template-columns: 116px 24px minmax(0, 1fr);
        column-gap: 12px;
        align-items: start;
    }

    .status-history-time {
        text-align: right;
        padding-top: 2px;
    }

    .status-history-date {
        font-size: 12px;
        line-height: 1.25;
        font-weight: 700;
        color: #64748b;
    }

    .status-history-clock {
        font-size: 12px;
        line-height: 1.2;
        font-weight: 500;
        color: #94a3b8;
    }

    .status-history-axis {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        align-self: stretch;
        padding-top: 3px;
    }

    .status-history-axis::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 15px;
        bottom: -12px;
        width: 2px;
        background: #dbe3ef;
        transform: translateX(-50%);
    }

    .status-history-item.is-last .status-history-axis::after {
        content: none;
    }

    .status-history-dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: var(--dot-color);
        flex-shrink: 0;
    }

    .status-history-line {
        display: none;
    }

    .status-history-content {
        padding-bottom: 18px;
    }

    .status-history-item.is-last .status-history-content {
        padding-bottom: 0;
    }

    .status-history-title {
        margin: 0;
        font-size: 16px;
        line-height: 1.2;
        font-weight: 700;
        color: #1e293b;
    }

    .status-history-subtitle {
        margin-top: 3px;
        font-size: 12px;
        line-height: 1.3;
        color: #64748b;
        font-weight: 500;
    }

    .status-history-note {
        margin-top: 6px;
        font-size: 12px;
        line-height: 1.35;
        color: #475569;
        font-style: italic;
        white-space: pre-line;
    }

    @media (max-width: 767.98px) {
        .wf-heading {
            font-size: 21px;
        }

        .wf-step-title {
            font-size: 16px;
        }

        .wf-step-desc,
        .wf-progress-text,
        .wf-btn,
        .wf-input,
        .wf-manual-btn {
            font-size: 16px;
        }

        .wf-progress-meta,
        .wf-meta-text,
        .wf-progress-title {
            font-size: 13px;
        }

        .status-history-item {
            grid-template-columns: 96px 20px minmax(0, 1fr);
            column-gap: 10px;
        }

        .status-history-title {
            font-size: 16px;
        }

        .status-history-date,
        .status-history-clock,
        .status-history-subtitle,
        .status-history-note {
            font-size: 13px;
        }
    }
</style>
@endpush

@push('ipal-scripts')
<script>
(() => {
    const workflowForm = document.getElementById('workflowForm');
    const uploadBtn = document.getElementById('followUpUploadBtn');
    const photoInput = document.getElementById('followUpPhotoInput');
    const uploadInfo = document.getElementById('followUpUploadInfo');
    const progressInput = document.getElementById('progressLogInput');
    const progressList = document.getElementById('progressLogList');
    const progressPayload = document.getElementById('progressLogPayload');
    const progressAutoSaveSubmit = document.getElementById('progressAutoSaveSubmit');
    const progressAddBtn = document.getElementById('progressAddBtn');
    const verifyAcceptBtn = document.getElementById('verifyAcceptBtn');
    const verifyRejectBtn = document.getElementById('verifyRejectBtn');
    const verifyInfoText = document.getElementById('verifyInfoText');
    const actorName = @json(auth()->user()->name ?? 'Admin Lapangan');

    const escapeHtml = (value) => String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const getMetaText = () => {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        return `Hari ini, ${time} - ${actorName}`;
    };

    const updatePayload = () => {
        if (!progressPayload || !progressList) {
            return;
        }

        const lines = Array.from(progressList.querySelectorAll('[data-log-text]')).map((item) => item.dataset.logText || '');
        progressPayload.value = lines.filter((line) => line.trim().length > 0).join('\n');
    };

    const appendLogCard = (text, metaText) => {
        if (!progressList) {
            return;
        }

        const item = document.createElement('div');
        item.className = 'wf-progress-item';
        item.dataset.logText = text;
        item.innerHTML = `
            <div class="wf-progress-text">${escapeHtml(text)}</div>
            <div class="wf-progress-meta">${escapeHtml(metaText)}</div>
        `;
        progressList.prepend(item);
    };

    const addPendingInputAsLog = () => {
        if (!progressInput) {
            return false;
        }

        const value = progressInput.value.trim();
        if (!value) {
            return false;
        }

        appendLogCard(value, getMetaText());
        progressInput.value = '';
        updatePayload();
        return true;
    };

    if (uploadBtn && photoInput) {
        uploadBtn.addEventListener('click', () => photoInput.click());
        photoInput.addEventListener('change', () => {
            if (!uploadInfo) {
                return;
            }

            if (photoInput.files && photoInput.files[0]) {
                const file = photoInput.files[0];
                uploadInfo.textContent = `${file.name} (${(file.size / 1024).toFixed(0)} KB) siap diunggah saat tombol workflow dikirim.`;
            } else {
                uploadInfo.textContent = '';
            }
        });
    }

    if (progressInput) {
        progressInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                const wasAdded = addPendingInputAsLog();
                if (wasAdded && progressAutoSaveSubmit) {
                    progressAutoSaveSubmit.click();
                }
            }
        });
    }

    if (progressAddBtn) {
        progressAddBtn.addEventListener('click', () => {
            addPendingInputAsLog();
        });
    }

    if (verifyAcceptBtn && verifyRejectBtn) {
        verifyAcceptBtn.addEventListener('click', () => {
            verifyRejectBtn.classList.add('d-none');
            if (verifyInfoText) {
                verifyInfoText.textContent = `Diverifikasi oleh: ${actorName}`;
                verifyInfoText.classList.remove('d-none');
            }
        });

        verifyRejectBtn.addEventListener('click', () => {
            verifyAcceptBtn.classList.add('d-none');
        });
    }

    if (workflowForm) {
        workflowForm.addEventListener('submit', () => {
            addPendingInputAsLog();
            updatePayload();
        });
    }

    if (progressPayload && progressPayload.value.trim() !== '' && progressList && progressList.children.length === 0) {
        const seededLogs = progressPayload.value
            .split(/\r\n|\r|\n/)
            .map((line) => line.trim())
            .filter((line) => line.length > 0);

        progressList.innerHTML = '';
        seededLogs.forEach((line) => appendLogCard(line, 'Draft catatan'));
        updatePayload();
    }
})();
</script>
@endpush
