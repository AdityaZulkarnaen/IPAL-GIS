@extends('ipal::layouts.main')

@include('ipal::components.data-jaringan.tailwind-assets')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid bg-white pt-4">
        <div class="mb-6">
            <div class="mb-2 text-[12px] md:text-[14px] text-[#6d7a94]">
                <span>IPAL</span>
                <span class="mx-1">&gt;</span>
                <span>Data Jaringan</span>
                <span class="mx-1">&gt;</span>
                <span class="font-semibold text-[#1d2a46]">Riwayat Upload</span>
            </div>
            <h2 class="text-[18px] md:text-[22px] font-bold text-[#1a2744] tracking-[-0.01em]">Riwayat Upload Data Jaringan</h2>
        </div>

        <div class="rounded-xl border border-[#e3e7ef] bg-white shadow-panel overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-4 border-b border-[#edf0f5]">
                <h3 class="text-[16px] font-semibold text-[#1d2a46]">Riwayat Upload</h3>

                <form method="GET" action="{{ route('ipal.upload.history') }}" class="w-full md:w-auto">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#98a2b8]" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M20 20L17 17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Cari Riwayat"
                            class="w-full md:w-[230px] h-[36px] rounded-lg border border-[#e1e5ee] bg-[#fafbfe] pl-9 pr-3 text-[13px] text-[#43506d] focus:outline-none focus:border-[#b9c6dd]"
                        >
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[860px] w-full border border-[#e3e7ef] border-collapse text-[13px] text-[#344054]">
                    <thead class="bg-white text-[#7b879f]">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium border border-[#edf0f5]">Tipe</th>
                            <th class="px-5 py-3 text-left font-medium border border-[#edf0f5]">Nama File</th>
                            <th class="px-5 py-3 text-center font-medium border border-[#edf0f5]">Jumlah Aset</th>
                            <th class="px-5 py-3 text-center font-medium border border-[#edf0f5]">Tanggal</th>
                            <th class="px-5 py-3 text-center font-medium border border-[#edf0f5]">Status</th>
                            <th class="px-5 py-3 text-center font-medium border border-[#edf0f5]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($uploads as $upload)
                            <tr class="hover:bg-[#fbfcff]">
                                <td class="px-5 py-3 border border-[#f0f3f8]">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold {{ $upload->tipe === 'manhole' ? 'bg-[#e7f2ff] text-[#2676eb]' : 'bg-[#edf6ff] text-[#4b88e9]' }}">
                                        {{ strtoupper($upload->tipe) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 border border-[#f0f3f8] text-[#1d2a46] font-medium break-all">{{ $upload->nama_file_asli }}</td>
                                <td class="px-5 py-3 border border-[#f0f3f8] text-center">{{ number_format((int) ($upload->total_fitur ?? 0), 0, ',', '.') }}</td>
                                <td class="px-5 py-3 border border-[#f0f3f8] text-center">{{ $upload->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-5 py-3 border border-[#f0f3f8] text-center">
                                    @if($upload->status === 'completed' && $upload->is_active)
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold bg-[#dff9e9] text-[#1f9d55]">Aktif</span>
                                    @elseif($upload->status === 'completed')
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold bg-[#eceff5] text-[#60708f]">Non aktif</span>
                                    @elseif($upload->status === 'failed')
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold bg-[#ffe6ea] text-[#ea4d6a]">Gagal</span>
                                    @elseif($upload->status === 'processing')
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold bg-[#fff4d8] text-[#c2871e]">Diproses</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold bg-[#eceff5] text-[#60708f]">Pending</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 border border-[#f0f3f8] text-center">
                                    <form method="POST" action="{{ route('ipal.upload.destroy', $upload->id) }}" class="inline-block js-upload-delete-form" data-confirm="Hapus data upload ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[#98a2b8] hover:text-[#d1435b]" title="Hapus">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.1" d="M13.8754 6.22559C13.9992 6.22323 14.1223 6.2444 14.2367 6.28906C14.3512 6.33378 14.4548 6.40111 14.5404 6.48535C14.626 6.56954 14.6924 6.66985 14.7348 6.7793C14.7771 6.88861 14.7954 7.00499 14.7875 7.12109L14.2768 15.0264C14.248 15.5223 14.0206 15.9899 13.6401 16.334C13.2594 16.678 12.7537 16.8738 12.226 16.8809L6.08049 16.9492C5.54541 16.9603 5.02581 16.7778 4.63029 16.4385C4.23475 16.099 3.993 15.6285 3.95549 15.126L3.2133 7.12891C3.20426 7.01205 3.22124 6.89453 3.26311 6.78418C3.30505 6.67371 3.37162 6.57239 3.45744 6.48731C3.54322 6.40231 3.64718 6.33518 3.76213 6.29004C3.87712 6.24491 4.00084 6.22314 4.12541 6.22559H13.8754ZM10.3774 2.0625C10.5787 2.06287 10.7736 2.13364 10.9281 2.2627C11.0827 2.392 11.1871 2.5722 11.2231 2.77051L11.4233 3.86231H6.60002L6.80803 2.7627C6.84723 2.56518 6.95406 2.38744 7.10978 2.25977C7.26542 2.13218 7.46031 2.0623 7.66154 2.0625H10.3774Z" fill="#78829D"/>
                                                <path d="M6.21038 17.0625C5.58521 17.0566 4.98514 16.8158 4.52939 16.3878C4.07363 15.9598 3.79555 15.3761 3.75038 14.7525L3.20288 6.92252C3.19796 6.84865 3.20763 6.77454 3.23135 6.70441C3.25507 6.63428 3.29236 6.56951 3.34112 6.51379C3.38987 6.45808 3.44911 6.41251 3.51547 6.37969C3.58184 6.34687 3.65401 6.32745 3.72788 6.32252C3.80175 6.3176 3.87586 6.32727 3.94599 6.35099C4.01612 6.37471 4.0809 6.41201 4.13661 6.46076C4.19233 6.50951 4.23789 6.56876 4.27071 6.63512C4.30353 6.70148 4.32295 6.77365 4.32788 6.84752L4.86038 14.6775C4.88697 15.0223 5.04346 15.3442 5.29823 15.5781C5.553 15.812 5.88705 15.9404 6.23288 15.9375L11.8954 15.87C12.2394 15.8666 12.5692 15.732 12.8173 15.4936C13.0654 15.2553 13.2132 14.9312 13.2304 14.5875L13.6729 6.84752C13.6844 6.69832 13.7534 6.55942 13.8653 6.46009C13.9773 6.36076 14.1234 6.30875 14.2729 6.31502C14.4208 6.32468 14.5589 6.39233 14.6572 6.50329C14.7556 6.61424 14.8061 6.75951 14.7979 6.90752L14.3554 14.655C14.3177 15.2814 14.0449 15.8704 13.5915 16.3043C13.1382 16.7381 12.5378 16.9848 11.9104 16.995L6.24788 17.0625H6.21038ZM11.2354 9.63752C11.2334 9.48894 11.1736 9.34699 11.0685 9.24192C10.9634 9.13685 10.8215 9.07696 10.6729 9.07502H7.32788C7.1787 9.07502 7.03562 9.13429 6.93013 9.23977C6.82464 9.34526 6.76538 9.48834 6.76538 9.63752C6.76538 9.78671 6.82464 9.92978 6.93013 10.0353C7.03562 10.1408 7.1787 10.2 7.32788 10.2H10.6729C10.748 10.202 10.8228 10.189 10.8927 10.1616C10.9627 10.1342 11.0265 10.0931 11.0804 10.0407C11.1342 9.98823 11.177 9.92555 11.2062 9.8563C11.2354 9.78706 11.2504 9.71267 11.2504 9.63752H11.2354ZM10.6504 12.4125C10.6504 12.2633 10.5911 12.1203 10.4856 12.0148C10.3801 11.9093 10.2371 11.85 10.0879 11.85H7.91288C7.7637 11.85 7.62062 11.9093 7.51513 12.0148C7.40964 12.1203 7.35038 12.2633 7.35038 12.4125C7.35038 12.5617 7.40964 12.7048 7.51513 12.8103C7.62062 12.9158 7.7637 12.975 7.91288 12.975H10.0879C10.2365 12.9731 10.3784 12.9132 10.4835 12.8081C10.5885 12.7031 10.6484 12.5611 10.6504 12.4125ZM15.7504 4.89002C15.7321 5.02387 15.6657 5.14646 15.5635 5.23483C15.4613 5.32319 15.3305 5.37126 15.1954 5.37002H15.1129C11.0644 4.81364 6.95885 4.81364 2.91038 5.37002C2.83596 5.38288 2.75973 5.38081 2.68611 5.36395C2.6125 5.3471 2.54296 5.31577 2.48156 5.27181C2.42015 5.22785 2.36809 5.17212 2.3284 5.10787C2.28872 5.04361 2.2622 4.97211 2.25038 4.89752C2.22658 4.75034 2.26219 4.59972 2.34938 4.47878C2.43657 4.35783 2.56822 4.27645 2.71538 4.25252C2.81288 4.25252 3.84788 4.07252 5.47538 3.94502L5.74538 2.53502C5.83107 2.08492 6.07136 1.67891 6.42469 1.3872C6.77803 1.09549 7.22219 0.936433 7.68038 0.937522H10.3279C10.7895 0.935321 11.2372 1.09571 11.5923 1.39056C11.9475 1.6854 12.1876 2.09589 12.2704 2.55002L12.5254 3.94502C13.4179 4.02002 14.3329 4.11002 15.2704 4.25252C15.3439 4.26264 15.4146 4.28719 15.4785 4.32476C15.5425 4.36232 15.5984 4.41217 15.643 4.47142C15.6876 4.53067 15.7201 4.59815 15.7385 4.67C15.7569 4.74184 15.761 4.81661 15.7504 4.89002ZM8.67038 3.81752H11.3554L11.1604 2.75252C11.1253 2.55917 11.0235 2.38425 10.8728 2.25818C10.7221 2.13211 10.5319 2.06287 10.3354 2.06252H7.68788C7.49154 2.06224 7.30119 2.13014 7.14935 2.25462C6.99751 2.3791 6.89361 2.55244 6.85538 2.74502L6.65288 3.81752H8.67038Z" fill="#78829D"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 border border-[#f0f3f8] text-center text-[#8793ab]">Belum ada riwayat upload.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-center gap-3 px-5 py-3 border-t border-[#edf0f5] text-[12px] text-[#7b879f]">
                <div class="flex flex-row gap-2 items-center">
                    <div>Menampilkan</div>
                        <select id="manhole-per-page" class="h-10 w-[96px] rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                        </select>
                    <div>per halaman</div>
                </div>
                {{-- <div>Menampilkan {{ $uploads->firstItem() ?? 0 }}-{{ $uploads->lastItem() ?? 0 }} dari {{ $uploads->total() }} data</div> --}}
                <div class="overflow-x-auto">{{ $uploads->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('manhole-per-page').addEventListener('change', function() {
        const url = new URL(window.location.href);

        url.searchParams.set('per_page', this.value);

        url.searchParams.set('page', 1);

        window.location.href = url.href;
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    document.querySelectorAll('.js-upload-delete-form').forEach((form) => {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const confirmMessage = this.dataset.confirm || 'Hapus data upload ini?';
            if (!window.confirm(confirmMessage)) {
                return;
            }

            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }

            try {
                const response = await fetch(this.action, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                let payload = null;
                try {
                    payload = await response.json();
                } catch (e) {
                    payload = null;
                }

                if (!response.ok || !payload?.success) {
                    alert(payload?.message || 'Gagal menghapus data upload.');
                    return;
                }

                window.location.reload();
            } catch (error) {
                alert('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });
    });
</script>
@endsection