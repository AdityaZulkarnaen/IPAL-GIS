<div class="bg-white border-2 border-[#e4e7ee] rounded-2xl shadow-panel h-fit overflow-hidden">
    <div class="px-6 py-5 border-b border-[#e8ebf1]">
        <h3 class="text-[18px] leading-[1.1] font-bold text-[#1a2744] tracking-[-0.02em]">Riwayat Upload</h3>
    </div>

    <div class="px-6 py-3">
        <ul class="list-none m-0 p-0">
            @forelse($uploads->getCollection()->take(7) as $upload)
                @php
                    $createdAt = $upload->created_at;
                    $tanggalLabel = $createdAt->isToday()
                        ? 'Hari ini'
                        : $createdAt->locale('id')->translatedFormat('j M Y');
                    $jamLabel = $createdAt->format('g:i A');
                    $jumlahAset = number_format((int) ($upload->total_fitur ?? 0), 0, ',', '.');
                @endphp
                <li class="relative py-3 pl-12 last:pb-1">
                    @if(!$loop->last)
                        <span class="absolute left-[16px] top-[46px] h-[calc(100%-20px)] w-px bg-[#d5dbe7]" aria-hidden="true"></span>
                    @endif

                    <span class="absolute left-0 top-3 inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-[#c9d1e2] bg-white text-[#7f8ca6]" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.2"/>
                            <path d="M4.9 8.2L7.1 10.2L11.1 6.2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>

                    <div class="text-[#1d2a46] text-[14px] leading-[1.3] font-semibold break-all">
                        Sukses upload <a href="javascript:void(0)" class="text-[#2474ea] hover:text-[#1f66cc]">{{ $upload->nama_file_asli }}</a>
                    </div>
                    <div class="mt-0.5 text-[12px] text-[#7e8ba5] leading-[1.35]">
                        {{ $tanggalLabel }}, {{ $jamLabel }} • {{ $jumlahAset }} Assets
                    </div>
                </li>
            @empty
                <li class="py-3 text-sm text-slate-500">Belum ada riwayat upload.</li>
            @endforelse
        </ul>
    </div>

    <div class="px-6 py-4 border-t border-[#e8ebf1] bg-white text-center">
        <a href="{{ route('ipal.upload.history') }}" class="inline-block text-[#2474ea] text-[16px] font-medium border-b border-dotted border-[#7eb1ff] leading-none pb-1 hover:text-[#1f66cc] hover:border-[#1f66cc]">
            Lihat Seluruh Riwayat
        </a>
    </div>
</div>
